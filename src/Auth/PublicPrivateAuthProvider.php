<?php

/*
 * This file is part of the Esat SDK Package.
 *
 * (c) e-satisfaction Developers <tech@e-satisfaction.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Esat\Auth;

use Esat\Http\Auth\AuthProviderInterface;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;

/**
 * Class PublicPrivateAuthProvider
 * @package Esat\Auth
 */
class PublicPrivateAuthProvider implements AuthProviderInterface
{
    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var string
     */
    private $privateKey;

    /**
     * PublicPrivateAuthProvider constructor.
     *
     * @param string $publicKey
     * @param string $privateKey
     *
     * @throws InvalidArgumentException
     */
    public function __construct($publicKey, $privateKey)
    {
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
    }

    /**
     * @param RequestInterface $request
     * @param string           $method
     *
     * @return RequestInterface|static
     * @throws InvalidArgumentException
     */
    public function setRequestAuth(RequestInterface $request, $method = 'GET')
    {
        // Generate hash
        $time = time();
        $method = strtoupper($method);
        $hash = base64_encode(hash_hmac('sha256', $this->getPublicKey() . $time . $method, $this->getPrivateKey(), true));

        // Set request headers
        $request = $request->withHeader('X-Public', $this->getPublicKey())
            ->withHeader('X-Microtime', $time)
            ->withHeader('X-HASH', $hash);

        return $request;
    }

    /**
     * @return string
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @param string $publicKey
     *
     * @return $this
     */
    public function setPublicKey(string $publicKey)
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @param string $privateKey
     *
     * @return $this
     */
    public function setPrivateKey(string $privateKey)
    {
        $this->privateKey = $privateKey;

        return $this;
    }
}
