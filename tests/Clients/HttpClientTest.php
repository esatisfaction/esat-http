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

use Esat\Http\Http_TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Class HttpClientTest
 * @package Esat\Http\Clients
 */
class HttpClientTest extends Http_TestCase
{
    /**
     * @var HttpClient
     */
    private $client;

    /**
     * {@inheritdoc}
     * @throws InvalidArgumentException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->client = new HttpClient();
    }

    /**
     * @covers \Esat\Http\Clients\HttpClient::buildRequest
     */
    public function testBuildRequest()
    {
        $method = Request::METHOD_GET;
        $uri = '/path/to/resource';
        $headers = [
            'test_h1' => 'test_v1',
            'test_h2' => 'test_v2',
        ];
        $this->client->buildRequest($method, $uri, $headers);
        $this->assertEquals($method, $this->client->getCurrentRequest()->getMethod());
        $this->assertEquals($uri, $this->client->getCurrentRequest()->getUri()->getPath());
        foreach ($headers as $key => $value) {
            $this->assertEquals($value, $this->client->getCurrentRequest()->getHeader($key)[0]);
        }
    }

    /**
     * @covers \Esat\Http\Clients\HttpClient::send
     */
    public function testSend()
    {
        // Mock client
        /** @var Client|MockObject $guzzle */
        $guzzle = $this->getMockBuilder(Client::class)
            ->onlyMethods(['send'])
            ->getMock();
        $this->client->setGuzzleClient($guzzle);

        // Prepare expected parameters and options
        $parameters = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $expectedOptions = [
            'form_params' => $parameters,
        ];

        // Mock send
        $response = new Response();
        $guzzle->method('send')->willReturn($response);
        $guzzle
            ->expects($this->once())
            ->method('send')
            /**
             * with() expects an array as the default argument, according to the docblock, which is not valid.
             * Instead, we are providing the expected arguments to match all the parameters of the call.
             * Type hinting might point it out as error according to docblock.
             */
            ->with(
                $this->callback(function (\GuzzleHttp\Psr7\Request $request) {
                    return $request->getMethod() == SymfonyRequest::METHOD_POST
                        && $request->getUri() == '/uri';
                }),
                $expectedOptions);

        // Send request
        $this->assertEquals($response, $this->client->send(Request::METHOD_POST, '/uri', [], $parameters));
    }
}
