<?php

/*
 * This file is part of the e-satisfaction.com Http Package.
 *
 * (c) e-satisfaction.com Developers <tech@e-satisfaction.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Esat\Http\Clients;

use Esat\Helpers\UuidHelper;
use Esat\Http\Http_TestCase;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Class AbstractHttpClientTest
 * @package Esat\Http\Clients
 */
class AbstractHttpClientTest extends Http_TestCase
{
    /**
     * @var AbstractHttpClient|MockObject
     */
    private $client;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        /** @var AbstractHttpClient|MockObject $client */
        $this->client = $this->getMockForAbstractClass(AbstractHttpClient::class);
    }

    /**
     * @covers \Esat\Http\Clients\AbstractHttpClient::__construct
     */
    public function testConstruct()
    {
        /** @var AbstractHttpClient|MockObject $client */
        $client = $this->getMockForAbstractClass(AbstractHttpClient::class);
        $this->assertTrue($client instanceof AbstractHttpClient);
        $this->assertNotNull($client->getGuzzleClient());
        $this->assertTrue($client->getGuzzleClient() instanceof Client);
    }

    /**
     * @covers \Esat\Http\Clients\AbstractHttpClient::setGuzzleClient
     * @covers \Esat\Http\Clients\AbstractHttpClient::getGuzzleClient
     * @covers \Esat\Http\Clients\AbstractHttpClient::setCurrentRequest
     * @covers \Esat\Http\Clients\AbstractHttpClient::getCurrentRequest
     * @covers \Esat\Http\Clients\AbstractHttpClient::setCurrentResponse
     * @covers \Esat\Http\Clients\AbstractHttpClient::getCurrentResponse
     * @covers \Esat\Http\Clients\AbstractHttpClient::setCurrentOptions
     * @covers \Esat\Http\Clients\AbstractHttpClient::getCurrentOptions
     */
    public function testGetterAndSetter()
    {
        // Guzzle client
        $guzzle = new Client();
        $this->assertTrue($this->client->setGuzzleClient($guzzle) instanceof AbstractHttpClient);
        $this->assertEquals($guzzle, $this->client->getGuzzleClient());

        // Current Request
        $request = new Request(SymfonyRequest::METHOD_GET, '');
        $this->assertTrue($this->client->setCurrentRequest($request) instanceof AbstractHttpClient);
        $this->assertEquals($request, $this->client->getCurrentRequest());

        // Current Response
        $response = new Response();
        $this->assertTrue($this->client->setCurrentResponse($response) instanceof AbstractHttpClient);
        $this->assertEquals($response, $this->client->getCurrentResponse());

        // Current Options
        $options = [
            'name' => 'value',
        ];
        $this->assertTrue($this->client->setCurrentOptions($options) instanceof AbstractHttpClient);
        $this->assertEquals($options, $this->client->getCurrentOptions());
    }

    /**
     * @covers \Esat\Http\Clients\AbstractHttpClient::buildOptions
     * @covers \Esat\Http\Clients\AbstractHttpClient::clearOptions
     * @covers \Esat\Http\Clients\AbstractHttpClient::setQuery
     * @covers \Esat\Http\Clients\AbstractHttpClient::setMultipart
     * @covers \Esat\Http\Clients\AbstractHttpClient::setFormParameters
     * @covers \Esat\Http\Clients\AbstractHttpClient::setOption
     */
    public function testBuildOptions()
    {
        // GET
        $parameters = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $options = $this->client->buildOptions(SymfonyRequest::METHOD_GET, $parameters);

        // Strictly assert, both arrays contain only the keys and values from the $parameters variable
        $this->assertTrue(
            empty(array_diff_assoc($parameters, $options['query'])) &&
            empty(array_diff_assoc($options['query'], $parameters))
        );

        // POST - Parameters
        $parameters = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $options = $this->client->buildOptions(SymfonyRequest::METHOD_POST, $parameters);

        // Strictly assert, both arrays contain only the keys and values from the $parameters variable
        $this->assertTrue(
            empty(array_diff_assoc($parameters, $options['form_params'])) &&
            empty(array_diff_assoc($options['form_params'], $parameters))
        );

        // POST - Multipart
        $parameters = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $multipart = [
            'file1' => 'value1',
            'file2' => 'value2',
        ];
        $options = $this->client->buildOptions(SymfonyRequest::METHOD_POST, $parameters, $multipart);

        // Loosely check that $multipart keys and values are contained in the compared array
        $this->assertTrue(empty(array_diff_assoc($multipart, $options['multipart'])));

        $this->assertEquals(array_keys($parameters)[0], $options['multipart'][0]['name']);
        $this->assertEquals($parameters['name1'], $options['multipart'][0]['contents']);
        $this->assertEquals(array_keys($parameters)[1], $options['multipart'][1]['name']);
        $this->assertEquals($parameters['name2'], $options['multipart'][1]['contents']);
    }

    /**
     * @covers \Esat\Http\Clients\AbstractHttpClient::setOption
     * @covers \Esat\Http\Clients\AbstractHttpClient::getOption
     * @covers \Esat\Http\Clients\AbstractHttpClient::setBody
     * @covers \Esat\Http\Clients\AbstractHttpClient::setJson
     * @covers \Esat\Http\Clients\AbstractHttpClient::clearOptions
     *
     * @throws Exception
     */
    public function testGetSetOptions()
    {
        // Body
        $value = UuidHelper::create();
        $this->assertTrue($this->client->setBody($value) instanceof AbstractHttpClient);
        $this->assertEquals($value, $this->client->getCurrentOptions()['body']);
        $this->assertEquals($value, $this->client->getOption('body'));

        // Json
        $value = UuidHelper::create();
        $this->assertTrue($this->client->setJson($value) instanceof AbstractHttpClient);
        $this->assertEquals($value, $this->client->getCurrentOptions()['json']);
        $this->assertEquals($value, $this->client->getOption('json'));

        // Clear options
        $this->client->clearOptions();
        $this->assertEmpty($this->client->getCurrentOptions());
    }
}
