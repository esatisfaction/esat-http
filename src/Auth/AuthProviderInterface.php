<?php

/*
 * This file is part of the Esat Http Package.
 *
 * (c) e-satisfaction Developers <tech@e-satisfaction.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Esat\Http\Auth;

use Psr\Http\Message\RequestInterface;

/**
 * Interface AuthProviderInterface
 * @package Esat\Http\Auth
 */
interface AuthProviderInterface
{
    /**
     * @param RequestInterface $request
     *
     * @return RequestInterface
     */
    public function setRequestAuth(RequestInterface $request);
}
