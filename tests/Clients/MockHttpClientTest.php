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
use GuzzleHttp\Psr7\Response;

/**
 * Class MockHttpClientTest
 * @package Esat\Http\Clients
 */
class MockHttpClientTest extends Http_TestCase
{
    /**
     * @var MockHttpClient
     */
    private $client;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->client = new MockHttpClient();
    }

    /**
     * @covers \Esat\Http\Clients\MockHttpClient::setMockResponse
     * @covers \Esat\Http\Clients\MockHttpClient::getMockResponse
     */
    public function testGetterAndSetter()
    {
        $response = new Response();
        $this->assertTrue($this->client->setMockResponse($response) instanceof MockHttpClient);
        $this->assertEquals($response, $this->client->getMockResponse());
    }

    /**
     * @covers \Esat\Http\Clients\MockHttpClient::send
     *
     */
    public function testSend()
    {
        $method = 'GET';
        $uri = '/path/to/resource';
        $this->client->send($method, $uri);
        $this->assertRequest($this->client->getCurrentRequest(), $method, $uri);
    }
}
