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
use InvalidArgumentException;
use Panda\Support\Helpers\ArrayHelper;
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
    protected $currentOptions = [];

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
     * @throws InvalidArgumentException
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
    public function buildRequest($method, $uri, array $headers = [], $body = null, $version = '1.1')
    {
        return $this->currentRequest = new Request($method, $uri, $headers, $body, $version);
    }

    /**
     * @param string $method
     * @param array  $parameters
     * @param array  $multipart
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function buildOptions($method, $parameters = [], $multipart = [])
    {
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

    /**
     * Clear current options array.
     */
    public function clearOptions()
    {
        $this->currentOptions = [];
    }

    /**
     * @param array $multipart
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setMultipart($multipart)
    {
        $this->setOption('multipart', $multipart);

        return $this;
    }

    /**
     * @param string|array $query
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setQuery($query)
    {
        $this->setOption('query', $query);

        return $this;
    }

    /**
     * @param array $parameters
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setFormParameters($parameters)
    {
        $this->setOption('form_params', $parameters);

        return $this;
    }

    /**
     * @param mixed $body
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setBody($body)
    {
        $this->setOption('body', $body);

        return $this;
    }

    /**
     * @param string $json
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setJson($json)
    {
        $this->setOption('json', $json);

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setOption($key, $value)
    {
        $this->currentOptions = ArrayHelper::set($this->currentOptions, $key, $value, true);

        return $this;
    }

    /**
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getOption($key, $default = null)
    {
        return ArrayHelper::get($this->currentOptions, $key, $default, true);
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
