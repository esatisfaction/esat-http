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

use Psr\Http\Message\RequestInterface;

/**
 * Class Http_TestCase
 * @package Esat\Http
 */
class Http_TestCase extends Base_TestCase
{
    /**
     * @var MockHttpClient
     */
    protected $mockHttpClient;

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function setUp()
    {
        parent::setUp();

        // Setup mock http client
        $this->mockHttpClient = new MockHttpClient();
    }

    /**
     * @param RequestInterface $request
     * @param string           $method
     * @param string           $uri
     * @param string           $version
     */
    public function assertRequest(RequestInterface $request, $method, $uri, $version = '1.1')
    {
        $this->assertEquals(strtoupper($method), strtoupper($request->getMethod()));
        $this->assertEquals($uri, $request->getUri()->getPath());
        $this->assertEquals($version, $request->getProtocolVersion());
    }

    /**
     * @return MockHttpClient
     */
    public function getMockHttpClient()
    {
        return $this->mockHttpClient;
    }
}
