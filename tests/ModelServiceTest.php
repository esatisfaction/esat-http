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

use Esat\Helpers\UuidHelper;
use Esat\Model\Mock\MockModel;
use Exception;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Monolog\Logger;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use ReflectionException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Class ModelServiceTest
 * @package Esat\Http
 */
class ModelServiceTest extends Http_TestCase
{
    /**
     * @var ModelService
     */
    private $service;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->service = new class(new Logger('Http')) extends ModelService {
            public function initModel()
            {
                $this->model = new MockModel();
            }
        };
    }

    /**
     * @covers \Esat\Http\ModelService::__construct
     * @covers \Esat\Http\ModelService::initModel
     */
    public function testConstruct()
    {
        /** @var BaseService|MockObject $client */
        $logger = new Logger('Http');
        /** @var ModelService|MockObject $client */
        $client = $this->getMockForAbstractClass(ModelService::class, [$logger]);
        $this->assertTrue($client instanceof ModelService);
        $this->assertNotNull($client->getLogger());
        $this->assertEquals($logger, $client->getLogger());
        $this->assertTrue($client->getLogger() instanceof LoggerInterface);

    }

    /**
     * @covers \Esat\Http\ModelService::setModel
     * @covers \Esat\Http\ModelService::getModel
     * @covers \Esat\Http\ModelService::clearModel
     */
    public function testGetAndSetModel()
    {
        /** @var BaseService|MockObject $client */
        $logger = new Logger('Http');
        /** @var ModelService|MockObject $client */
        $client = $this->getMockForAbstractClass(ModelService::class, [$logger]);

        // Set model
        $model = new MockModel();
        $this->assertTrue($client->setModel($model) instanceof ModelService);
        $this->assertEquals($model, $client->getModel());

        // Clear model
        $this->assertNull($client->clearModel());
        $this->assertNull($client->getModel());
    }

    /**
     * @covers \Esat\Http\ModelService::checkModel
     */
    public function testCheckModel_Exception()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service->clearModel();
        $this->service->checkModel();
    }

    /**
     * @covers \Esat\Http\ModelService::initModelWithParameters
     * @covers \Esat\Http\ModelService::updateModelWithParameters
     * @covers \Esat\Http\ModelService::initModel
     * @covers \Esat\Http\ModelService::checkModel
     * @covers \Esat\Http\ModelService::getModel
     *
     * @throws ReflectionException
     * @throws Exception
     */
    public function testInitModelWithParameters()
    {
        $parameters = [
            'protected' => UuidHelper::create(),
        ];
        $this->service->initModelWithParameters($parameters);
        $this->assertNotEmpty($this->service->getModel());
        $this->assertEquals($parameters['protected'], $this->service->getModel()->getPropertyByName('protected'));
    }

    /**
     * @covers \Esat\Http\ModelService::getResponseAsArray
     *
     * @throws Exception
     */
    public function testGetResponseAsArray()
    {
        $array = [
            'name' => 'value',
        ];
        $response = new Response(SymfonyResponse::HTTP_OK, [], json_encode($array, JSON_FORCE_OBJECT));
        $this->assertEquals($array, $this->service->getResponseAsArray($response));
    }

    /**
     * @covers \Esat\Http\ModelService::getResponseAsArray
     */
    public function testGetResponseAsArray_Invalid()
    {
        $this->expectException(Exception::class);
        $response = new Response(SymfonyResponse::HTTP_OK, [], 'not_json');
        $this->service->getResponseAsArray($response);
    }

    /**
     * @covers \Esat\Http\ModelService::setModelFromArray
     *
     * @throws ReflectionException
     * @throws Exception
     */
    public function testSetModelFromArray()
    {
        $model = new MockModel();
        $array = $model->toArray();
        $this->assertTrue($this->service->setModelFromArray($array, $model, null, true));
    }

    /**
     * @covers \Esat\Http\ModelService::setModelFromArray
     *
     * @throws ReflectionException
     * @throws Exception
     */
    public function testSetModelFromArray_Subfield()
    {
        $model = new MockModel();
        $array = [
            'sub' => $model->toArray(),
        ];
        $this->assertTrue($this->service->setModelFromArray($array, $model, 'sub', true));
    }

    /**
     * @covers \Esat\Http\ModelService::setModelFromArray
     *
     * @throws Exception
     */
    public function testSetModelFromArray_EmptyArray()
    {
        $this->expectException(Exception::class);
        $this->assertTrue($this->service->setModelFromArray([], $model, null, true));
    }

    /**
     * @covers \Esat\Http\ModelService::setModelFromResponse
     *
     * @throws ReflectionException
     * @throws Exception
     */
    public function testSetModelFromResponse()
    {
        $model = new MockModel();
        $response = new Response(SymfonyResponse::HTTP_OK, [], $model->toJson());
        $this->assertTrue($this->service->setModelFromResponse($response));
    }

    /**
     * @covers \Esat\Http\ModelService::setModelFromResponse
     *
     * @throws Exception
     */
    public function testSetModelFromResponse_InvalidResponse()
    {
        $this->expectException(Exception::class);
        $response = new Response(SymfonyResponse::HTTP_OK, [], 'not_json');
        $this->assertTrue($this->service->setModelFromResponse($response));
    }
}
