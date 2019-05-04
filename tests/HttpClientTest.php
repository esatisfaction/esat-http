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

/**
 * Class HttpClientTest
 * @package Esat\Http
 */
class HttpClientTest extends Http_TestCase
{
    /**
     * @var HttpClient
     */
    private $client;

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function setUp()
    {
        parent::setUp();

        $this->client = new HttpClient();
    }

    /**
     * @covers \Esat\Http\HttpClient::buildRequest
     */
    public function testBuildRequest()
    {
        $method = 'GET';
        $uri = '/path/to/resource';
        $headers = [
            'test_h1' => 'test_v1',
            'test_h2' => 'test_v2',
        ];
        $this->client->buildRequest($method, $uri, $headers);

        // Assert
        $this->assertEquals($method, $this->client->getCurrentRequest()->getMethod());
        $this->assertEquals($uri, $this->client->getCurrentRequest()->getUri()->getPath());
        foreach ($headers as $key => $value) {
            $this->assertEquals($value, $this->client->getCurrentRequest()->getHeader($key)[0]);
        }
    }

    /**
     * @covers \Esat\Http\HttpClient::setQuery
     * @covers \Esat\Http\HttpClient::setBody
     * @covers \Esat\Http\HttpClient::setFormParameters
     * @covers \Esat\Http\HttpClient::setJson
     * @covers \Esat\Http\HttpClient::buildOptions
     *
     * @throws \InvalidArgumentException
     */
    public function testBuildOptions()
    {
        $this->client->setQuery('test_query');
        $this->assertEquals('test_query', $this->client->getCurrentOptions()['query']);
        $this->assertEquals('test_query', $this->client->getOption('query'));

        $this->client->setBody('test_body');
        $this->assertEquals('test_body', $this->client->getCurrentOptions()['body']);
        $this->assertEquals('test_body', $this->client->getOption('body'));

        $parameters = ['key' => 'value'];
        $this->client->setFormParameters($parameters);
        $this->assertEquals($parameters, $this->client->getCurrentOptions()['form_params']);
        $this->assertEquals($parameters, $this->client->getOption('form_params'));

        $parameters = ['key' => 'value'];
        $json = json_encode($parameters);
        $this->client->setJson($json);
        $this->assertEquals($json, $this->client->getCurrentOptions()['json']);
        $this->assertEquals($json, $this->client->getOption('json'));

        // Build mock options
        $this->client->clearOptions();
        $this->client->buildOptions('GET', $parameters);
        $this->assertEquals($parameters, $this->client->getOption('query'));

        // Build mock options
        $this->client->clearOptions();
        $this->client->buildOptions('POST', $parameters);
        $this->assertEquals($parameters, $this->client->getOption('form_params'));
    }
}
