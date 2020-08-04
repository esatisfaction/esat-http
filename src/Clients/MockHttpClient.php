<?php

/*
 * This file is part of the e-satisfaction Http Package.
 *
 * (c) e-satisfaction Developers <tech@e-satisfaction.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Esat\Http\Clients;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Class MockHttpClient
 * @package Esat\Http\Clients
 */
class MockHttpClient extends HttpClient
{
    /**
     * @var ResponseInterface
     */
    protected $mockResponse;

    /**
     * {@inheritdoc}
     */
    public function send($method, $uri, array $headers = [], $parameters = [], $multipart = [], $version = '1.1', $clearOptions = true)
    {
        // Build request
        $this->buildRequest($method, $uri, $headers, null, $version);

        // Build options
        $this->buildOptions($method, $parameters, $multipart, $clearOptions);

        // Return mock response
        return $this->getMockResponse();
    }

    /**
     * @return ResponseInterface
     */
    public function getMockResponse()
    {
        return $this->mockResponse ?: new Response();
    }

    /**
     * @param ResponseInterface $mockResponse
     *
     * @return $this
     */
    public function setMockResponse(ResponseInterface $mockResponse)
    {
        $this->mockResponse = $mockResponse;

        return $this;
    }
}
