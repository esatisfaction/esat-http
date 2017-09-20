<?php

namespace HttpAdapter;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Class MockHttpClient
 * @package Esat\Http
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
    public function send($method, $uri, array $headers = [], $parameters = [], $multipart = [], $version = '1.1')
    {
        // Build request
        $this->buildRequest($method, $uri, $headers, null, $version);

        // Build options
        $this->buildOptions($method, $parameters, $multipart);

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
