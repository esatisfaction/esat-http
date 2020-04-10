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

use Esat\Http\Auth\AuthProviderInterface;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;

/**
 * Class AuthClient
 * @package Esat\Http\Clients
 */
class AuthClient extends HttpClient
{
    /**
     * @var AuthProviderInterface
     */
    private $authProvider;

    /**
     * AuthClient constructor.
     *
     * @param AuthProviderInterface $authProvider
     *
     * @throws InvalidArgumentException
     */
    public function __construct(AuthProviderInterface $authProvider)
    {
        parent::__construct();
        $this->authProvider = $authProvider;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $headers
     * @param null   $body
     * @param string $version
     *
     * @return RequestInterface
     */
    public function buildRequest($method, $uri, array $headers = [], $body = null, $version = '1.1')
    {
        // Build normal request
        parent::buildRequest($method, $uri, $headers, $body, $version);

        // Set request authentication
        return $this->authProvider->setRequestAuth($this->currentRequest);
    }

    /**
     * @return AuthProviderInterface
     */
    public function getAuthProvider()
    {
        return $this->authProvider;
    }

    /**
     * @param AuthProviderInterface $authProvider
     *
     * @return $this
     */
    public function setAuthProvider(AuthProviderInterface $authProvider)
    {
        $this->authProvider = $authProvider;

        return $this;
    }
}
