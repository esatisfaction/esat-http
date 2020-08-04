<?php

/*
 * This file is part of the e-satisfaction.com Http Package.
 *
 * (c) e-satisfaction.com Developers <tech@e-satisfaction.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Esat\Http\Auth;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;

/**
 * Class MockAuthProvider
 * @package Esat\Http\Auth
 */
class MockAuthProvider implements AuthProviderInterface
{
    /**
     * @var string
     */
    private $headerName;

    /**
     * @var string
     */
    private $headerValue;

    /**
     * MockAuthProvider constructor.
     *
     * @param string $headerName
     * @param string $headerValue
     */
    public function __construct($headerName, $headerValue)
    {
        $this->headerName = $headerName;
        $this->headerValue = $headerValue;
    }

    /**
     * @param RequestInterface $request
     *
     * @return RequestInterface The new request built with authentication.
     * @throws InvalidArgumentException
     */
    public function setRequestAuth(RequestInterface &$request)
    {
        return $request = $request->withHeader($this->headerName, $this->headerValue);
    }
}
