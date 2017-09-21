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

use Esat\Http\Base_TestCase;
use GuzzleHttp\Psr7\Request;

/**
 * Class PublicPrivateAuthProviderTest
 * @package Esat\Http\Auth
 */
class PublicPrivateAuthProviderTest extends Base_TestCase
{
    /**
     * @covers \Esat\Http\Auth\PublicPrivateAuthProvider::setRequestAuth
     *
     * @throws \InvalidArgumentException
     */
    public function testSetRequestAuth()
    {
        // Create provider
        $publicKey = 'public_key';
        $privateKey = 'private_key';
        $provider = new PublicPrivateAuthProvider($publicKey, $privateKey);

        // Create mock request
        $request = new Request('GET', '/path/to/resource', ['test_header' => 'test_value']);
        $this->assertEquals('test_value', $request->getHeader('test_header')[0]);

        // Generate mock hash
        $time = time();
        $hash = base64_encode(hash_hmac('sha256', $publicKey . $time . $request->getMethod(), $privateKey, true));

        // Set auth to request
        $request2 = $provider->setRequestAuth($request, $time);

        // Assert request headers
        $this->assertEquals($request->getHeaders(), $request2->getHeaders());
        $this->assertEquals($publicKey, $request->getHeader('X-Public')[0]);
        $this->assertEquals($time, $request->getHeader('X-Microtime')[0]);
        $this->assertEquals($hash, $request->getHeader('X-Hash')[0]);
    }
}
