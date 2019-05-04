<?php

/*
 * This file is part of the e-satisfaction Http Package.
 *
 * (c) e-satisfaction Developers <tech@e-satisfaction.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Esat\Http;

use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class HttpClient
 * @package Esat\Http
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
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
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

    /**
     * @param string $method
     * @param array  $parameters
     * @param array  $multipart
     * @param bool   $clear
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function buildOptions($method, $parameters = [], $multipart = [], $clear = true)
    {
        // Clear options
        if ($clear) {
            $this->clearOptions();
        }

        // Set options
        if (in_array(strtoupper($method), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            if (!empty($multipart)) {
                foreach ($parameters as $name => $value) {
                    $multipart[] = ['name' => $name, 'contents' => $value];
                }
                $this->setMultipart($multipart);
            } else if (!empty($parameters)) {
                $this->setFormParameters($parameters);
            }
        } else if (!empty($parameters)) {
            $this->setQuery($parameters);
        }

        return $this->getCurrentOptions();
    }
}
