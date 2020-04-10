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

use Esat\Http\Http_TestCase;
use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;

/**
 * Class MockAuthProviderTest
 * @package Esat\Http\Auth
 */
class MockAuthProviderTest extends Http_TestCase
{
    /**
     * @covers \Esat\Http\Auth\MockAuthProvider::setRequestAuth
     *
     * @throws InvalidArgumentException
     */
    public function testSetRequestAuth()
    {
        // Create mock auth provider
        $headerName = 'header-name';
        $headerValue = 'header-value';
        $provider = new MockAuthProvider($headerName, $headerValue);

        // Create mock request
        $request = new Request('GET', '/path/to/resource');

        // Set auth to request
        $provider->setRequestAuth($request);

        // Assert values
        $this->assertNotNull($request->getHeader($headerName));
        $this->assertEquals($headerValue, $request->getHeader($headerName)[0]);
    }
}
