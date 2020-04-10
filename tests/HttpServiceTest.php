<?php

/*
 * This file is part of the Esat Http Package.
 *
 * (c) e-satisfaction Developers <tech@e-satisfaction.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Esat\Http;

use Esat\Http\Clients\MockHttpClient;
use Esat\Http\Config\Connection;
use Exception;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Class HttpServiceTest
 * @package Esat\Http
 */
class HttpServiceTest extends Base_TestCase
{
    /**
     * @var MockHttpClient
     */
    protected $mockHttpClient;

    /**
     * @var HttpService
     */
    protected $httpService;

    /**
     * @throws InvalidArgumentException
     * @throws \PHPUnit\Framework\Exception
     */
    public function setUp()
    {
        parent::setUp();

        // Setup mock http client
        $this->mockHttpClient = new MockHttpClient();
        $this->httpService = $this->getMockForAbstractClass(HttpService::class, [Connection::getInstance(), $this->getMockHttpClient(), new Logger('esat.sdk')]);
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
        $this->getMockHttpClient()->setMockResponse(new Response(SymfonyResponse::HTTP_NOT_FOUND, [], json_encode($bodyWithError)));

        // Assert response
        $this->assertEquals($this->httpService->getErrorFromLastResponse($this->getMockHttpClient()->getMockResponse()), $bodyWithError['error']);

        // Create
        $bodyWithMessage = [
            'message' => substr(md5(rand()), 20),
        ];
        $this->getMockHttpClient()->setMockResponse(new Response(SymfonyResponse::HTTP_NOT_FOUND, [], json_encode($bodyWithMessage)));

        // Assert response
        $this->assertEquals($this->httpService->getErrorFromLastResponse($this->getMockHttpClient()->getMockResponse()), $bodyWithMessage['message']);
    }

    /**
     * @return MockHttpClient
     */
    public function getMockHttpClient()
    {
        return $this->mockHttpClient;
    }
}
