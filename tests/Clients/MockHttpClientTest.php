<?php

/*
 * This file is part of the Esat Http Package.
 *
 * (c) e-satisfaction Developers <tech@e-satisfaction.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Esat\Http\Clients;

use Esat\Http\Http_TestCase;

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
