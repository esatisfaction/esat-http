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

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface HttpClientInterface
 * @package Esat\Http
 */
interface HttpClientInterface
{
    /**
     * @param string $method
     * @param string $uri
     * @param array  $headers
     * @param array  $parameters
     * @param array  $multipart
     * @param string $version
     *
     * @return mixed|ResponseInterface
     */
    public function send($method, $uri, array $headers = [], $parameters = [], $multipart = [], $version = '1.1');

    /**
     * @param string $method
     * @param string $uri
     * @param array  $headers
     * @param null   $body
     * @param string $version
     *
     * @return Request
     */
    public function buildRequest($method, $uri, array $headers = [], $body = null, $version = '1.1');

    /**
     * @param string $method
     * @param array  $parameters
     * @param array  $multipart
     *
     * @return array
     */
    public function buildOptions($method, $parameters = [], $multipart = []);

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setOption($key, $value);

    /**
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getOption($key, $default = null);
}
