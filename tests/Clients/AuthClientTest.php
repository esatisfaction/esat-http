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

use Esat\Http\Auth\MockAuthProvider;
use Esat\Http\Http_TestCase;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Class AuthClientTest
 * @package Esat\Http\Clients
 */
class AuthClientTest extends Http_TestCase
{
    /**
     * @covers \Esat\Http\Clients\AuthClient::__construct
     * @covers \Esat\Http\Clients\AuthClient::setAuthProvider
     * @covers \Esat\Http\Clients\AuthClient::getAuthProvider
     */
    public function testConstruct()
    {
        $auth = new MockAuthProvider('auth', 'token');
        $client = new AuthClient($auth);
        $this->assertTrue($client instanceof AuthClient);
        $this->assertEquals($auth, $client->getAuthProvider());
    }

    /**
     * @covers \Esat\Http\Clients\AuthClient::buildRequest
     */
    public function testBuildRequest()
    {
        $auth = new MockAuthProvider('auth', 'token');
        $client = new AuthClient($auth);
        $request = $client->buildRequest(SymfonyRequest::METHOD_GET, '');
        $this->assertTrue($request instanceof Request);
        $this->assertEquals('token', $request->getHeader('auth')[0]);
    }
}
