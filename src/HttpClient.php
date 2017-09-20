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

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class HttpClient
 * @package Esat\Http
 */
class HttpClient
{
    /**
     * @var Client
     */
    protected $guzzleClient;

    /**
     * @var RequestInterface
     */
    protected $currentRequest;

    /**
     * @var array
     */
    protected $currentOptions;

    /**
     * @var ResponseInterface
     */
    protected $currentResponse;

    /**
     * HttpClient constructor.
     */
    public function __construct()
    {
        $this->guzzleClient = new Client();
    }

    /**
     * @param string              $method
     * @param string|UriInterface $uri
     * @param array               $headers
     * @param array               $parameters
     * @param array               $multipart
     * @param string              $version
     *
     * @return mixed|ResponseInterface
     */
    public function send($method, $uri, array $headers = [], $parameters = [], $multipart = [], $version = '1.1')
    {
        // Build request
        $this->buildRequest($method, $uri, $headers, null, $version);

        // Build options
        $this->buildOptions($method, $parameters, $multipart);

        // Send request
        return $this->currentResponse = $this->guzzleClient->send($this->getCurrentRequest(), $this->getCurrentOptions());
    }

    /**
     * @param string     $method
     * @param string     $uri
     * @param array      $headers
     * @param mixed|null $body
     * @param string     $version
     *
     * @return Request
     */
    protected function buildRequest($method, $uri, array $headers = [], $body = null, $version = '1.1')
    {
        return $this->currentRequest = new Request($method, $uri, $headers, $body, $version);
    }

    /**
     * @param string $method
     * @param array  $parameters
     * @param array  $multipart
     *
     * @return array
     */
    protected function buildOptions($method, $parameters = [], $multipart = [])
    {
        // Set options
        $options = [];
        if (in_array(strtolower($method), ['post', 'put', 'patch', 'delete'])) {
            if (!empty($multipart)) {
                foreach ($parameters as $name => $value) {
                    $multipart[] = ['name' => $name, 'contents' => $value];
                }
                $options['multipart'] = $multipart;
            } else {
                $options['form_params'] = $parameters;
            }
        } else if (!empty($parameters)) {
            $options['query'] = $parameters;
        }

        return $this->currentOptions = $options;
    }

    protected function setOptions($key, $value)
    {
        $this->currentOptions[$key] = $value;
    }

    /**
     * @return Client
     */
    public function getGuzzleClient()
    {
        return $this->guzzleClient;
    }

    /**
     * @return RequestInterface
     */
    public function getCurrentRequest()
    {
        return $this->currentRequest;
    }

    /**
     * @return array
     */
    public function getCurrentOptions()
    {
        return $this->currentOptions;
    }

    /**
     * @return ResponseInterface
     */
    public function getCurrentResponse()
    {
        return $this->currentResponse;
    }
}
