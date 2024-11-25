<?php

/*
 * This file is part of the e-satisfaction.com Http Package.
 *
 * (c) e-satisfaction.com Developers <tech@e-satisfaction.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Esat\Http;

use Esat\Helpers\UuidHelper;
use Esat\Http\Clients\MockHttpClient;
use Esat\Http\Config\Connection;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Monolog\Logger;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Class HttpServiceTest
 * @package Esat\Http
 */
class HttpServiceTest extends Base_TestCase
{
    /**
     * @var HttpService
     */
    protected $httpService;

    /**
     * @throws InvalidArgumentException
     * @throws \PHPUnit\Framework\Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        // Setup mock http client
        $this->httpService = $this->getMockForAbstractClass(HttpService::class, [Connection::getInstance(), new MockHttpClient(), new Logger('esat.sdk')]);
    }

    /**
     * @covers \Esat\Http\HttpService::__construct
     */
    public function testConstruct()
    {
        // Setup mock http client
        $service = $this->getMockForAbstractClass(HttpService::class, [Connection::getInstance(), new MockHttpClient(), new Logger('esat.sdk')]);
        $this->assertTrue($service instanceof HttpService);
    }

    /**
     * @covers \Esat\Http\HttpService::setLastResponse
     * @covers \Esat\Http\HttpService::getLastResponse
     * @covers \Esat\Http\HttpService::setHttpClient
     * @covers \Esat\Http\HttpService::getHttpClient
     * @covers \Esat\Http\HttpService::getProcessId
     * @covers \Esat\Http\HttpService::setCacheEnabled
     * @covers \Esat\Http\HttpService::isCacheEnabled
     * @covers \Esat\Http\HttpService::setConnection
     * @covers \Esat\Http\HttpService::getConnection
     * @covers \Esat\Http\HttpService::setCache
     * @covers \Esat\Http\HttpService::getCache
     *
     * @throws Exception
     */
    public function testGetterAndSetter()
    {
        // Last response
        $value = new Response();
        $this->assertTrue($this->httpService->setLastResponse($value) instanceof HttpService);
        $this->assertEquals($value, $this->httpService->getLastResponse());

        // Http client
        $value = new MockHttpClient();
        $this->assertTrue($this->httpService->setHttpClient($value) instanceof HttpService);
        $this->assertEquals($value, $this->httpService->getHttpClient());

        // Process id
        $this->assertNotEmpty($this->httpService->getProcessId());

        // Cache enabled
        $value = rand(0, 1);
        $this->assertTrue($this->httpService->setCacheEnabled($value) instanceof HttpService);
        $this->assertEquals($value, $this->httpService->isCacheEnabled());

        // Connection
        $value = Connection::getInstance();
        $this->assertTrue($this->httpService->setConnection($value) instanceof HttpService);
        $this->assertEquals($value, $this->httpService->getConnection());

        // Cache
        $value = new ArrayAdapter();
        HttpService::setCache($value);
        $this->assertEquals($value, HttpService::getCache());
    }

    /**
     * @covers \Esat\Http\HttpService::send
     * @covers \Esat\Http\HttpService::getApiUri
     * @covers \Esat\Http\HttpService::getCacheKey
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testSend_Post()
    {
        // Mock client
        /** @var MockHttpClient|MockObject $client */
        $client = $this->getMockBuilder(MockHttpClient::class)
            ->setMethods(['send'])
            ->getMock();
        $this->httpService->setHttpClient($client);

        // Mock response
        $response = new Response();
        $client->method('send')->willReturn($response);
        $client
            ->expects($this->once())
            ->method('send')
            /**
             * with() expects an array as the default argument, according to the docblock, which is not valid.
             * Instead, we are providing the expected arguments to match all the parameters of the call.
             * Type hinting might point it out as error according to docblock.
             */
            ->with(Request::METHOD_POST, $this->httpService->getApiUri('/'));

        // Send
        $this->assertEquals($response, $this->httpService->send(Request::METHOD_POST, '/'));
    }

    /**
     * @covers \Esat\Http\HttpService::send
     * @covers \Esat\Http\HttpService::getApiUri
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testSend_Get_FromCache()
    {
        // Mock client
        /** @var MockHttpClient|MockObject $client */
        $client = $this->getMockBuilder(MockHttpClient::class)
            ->setMethods(['send'])
            ->getMock();
        $this->httpService->setHttpClient($client);

        // Set response in cache
        $cacheResponse = new Response(SymfonyResponse::HTTP_ALREADY_REPORTED);
        $serviceUri = $this->httpService->getApiUri('/');
        $parameters = [];
        $key = $this->httpService->getCacheKey($serviceUri, $parameters);
        $this->httpService->addToCache($key, $cacheResponse);

        // Send
        $response = $this->httpService->send(Request::METHOD_GET, '/', $parameters);
        $this->assertEquals($cacheResponse->getStatusCode(), $response->getStatusCode());
    }

    /**
     * @covers \Esat\Http\HttpService::send
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testSend_ServerException()
    {
        // Mock client
        /** @var MockHttpClient|MockObject $client */
        $client = $this->getMockBuilder(MockHttpClient::class)
            ->setMethods(['send'])
            ->getMock();
        $this->httpService->setHttpClient($client);

        // Prepare exception
        $response = new Response();
        $client->method('send')->willThrowException(new ServerException('Error', new \GuzzleHttp\Psr7\Request(Request::METHOD_POST, ''), $response));

        // Send
        $this->assertEquals($response, $this->httpService->send(Request::METHOD_POST, '/'));
    }

    /**
     * @covers \Esat\Http\HttpService::send
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testSend_ClientException()
    {
        // Mock client
        /** @var MockHttpClient|MockObject $client */
        $client = $this->getMockBuilder(MockHttpClient::class)
            ->setMethods(['send'])
            ->getMock();
        $this->httpService->setHttpClient($client);

        // Prepare exception
        $response = new Response();
        $client->method('send')->willThrowException(new ClientException('Error', new \GuzzleHttp\Psr7\Request(Request::METHOD_POST, ''), $response));

        // Send
        $this->assertEquals($response, $this->httpService->send(Request::METHOD_POST, '/'));
    }

    /**
     * @covers \Esat\Http\HttpService::send
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testSend_RuntimeException()
    {
        // Mock client
        /** @var MockHttpClient|MockObject $client */
        $client = $this->getMockBuilder(MockHttpClient::class)
            ->setMethods(['send'])
            ->getMock();
        $this->httpService->setHttpClient($client);

        // Prepare exception
        $client->method('send')->willThrowException(new RuntimeException('Error'));

        // Send
        $this->assertNull($this->httpService->send(Request::METHOD_POST, '/'));
    }

    /**
     * @covers \Esat\Http\HttpService::getResponseAsArray
     *
     * @throws Exception
     */
    public function testGetResponseAsArray()
    {
        $array = [
            'name' => 'value',
        ];
        $response = new Response(SymfonyResponse::HTTP_OK, [], json_encode($array, JSON_FORCE_OBJECT));
        $this->assertEquals($array, $this->httpService->getResponseAsArray($response, true));
        $this->assertEquals($response, $this->httpService->getLastResponse());
    }

    /**
     * @covers \Esat\Http\HttpService::getErrorFromLastResponse
     *
     * @throws Exception
     */
    public function testGetErrorFromLastResponse()
    {
        // Create
        $bodyWithError = [
            'error' => substr(md5(rand()), 20),
        ];
        $response = new Response(SymfonyResponse::HTTP_NOT_FOUND, [], json_encode($bodyWithError));
        $this->assertEquals($this->httpService->getErrorFromLastResponse($response), $bodyWithError['error']);

        // Create
        $bodyWithMessage = [
            'message' => substr(md5(rand()), 20),
        ];
        $response = new Response(SymfonyResponse::HTTP_NOT_FOUND, [], json_encode($bodyWithMessage));
        $this->assertEquals($this->httpService->getErrorFromLastResponse($response), $bodyWithMessage['message']);
    }

    /**
     * @covers \Esat\Http\HttpService::addToCache
     * @covers \Esat\Http\HttpService::getFromCache
     *
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws Exception
     */
    public function testCache()
    {
        $key = UuidHelper::create();
        $response = new Response(SymfonyResponse::HTTP_OK, [], 'Body');
        $this->assertTrue($this->httpService->addToCache($key, $response, 100) instanceof HttpService);
        $cacheResponse = $this->httpService->getFromCache($key);
        $this->assertEquals($response->getStatusCode(), $cacheResponse->getStatusCode());

        // Empty response
        $this->assertNull($this->httpService->getFromCache('other key'));
    }

    /**
     * @covers \Esat\Http\HttpService::addToCache
     * @covers \Esat\Http\HttpService::getFromCache
     * @covers \Esat\Http\HttpService::normalizeCacheKey
     * @covers \Esat\Http\HttpService::getResponseFromArray
     * @covers \Esat\Http\HttpService::getResponseToArray
     *
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws Exception
     */
    public function testAddCache_Exceptions()
    {
        $key = UuidHelper::create();
        $response = new Response(SymfonyResponse::HTTP_OK, [], 'Body');
        $this->assertTrue($this->httpService->addToCache($key, $response, 100) instanceof HttpService);
        $cacheResponse = $this->httpService->getFromCache($key);
        $this->assertEquals($response->getStatusCode(), $cacheResponse->getStatusCode());

        // Empty response
        $this->assertNull($this->httpService->getFromCache('other key'));

        // InvalidArgumentException
        $this->assertTrue($this->httpService->addToCache('', null) instanceof HttpService);
    }
}
