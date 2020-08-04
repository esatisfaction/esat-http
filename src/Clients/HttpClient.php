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

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class HttpClient
 * @package Esat\Http\Clients
 */
class HttpClient extends AbstractHttpClient implements HttpClientInterface
{
    /**
     * @param string              $method
     * @param string|UriInterface $uri
     * @param array               $headers
     * @param array               $parameters
     * @param array               $multipart
     * @param string              $version
     * @param bool                $clearOptions
     *
     * @return mixed|ResponseInterface
     */
    public function send($method, $uri, array $headers = [], $parameters = [], $multipart = [], $version = '1.1', $clearOptions = true)
    {
        // Build request
        $this->buildRequest($method, $uri, $headers, null, $version);

        // Build options
        $this->buildOptions($method, $parameters, $multipart, $clearOptions);

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
    public function buildRequest($method, $uri, array $headers = [], $body = null, $version = '1.1')
    {
        return $this->currentRequest = new Request($method, $uri, $headers, $body, $version);
    }
}
