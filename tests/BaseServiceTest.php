<?php

/*
 * This file is part of the e-satisfaction.com Http Package.
 *
 * (c) e-satisfaction.com Developers <tech@e-satisfaction.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Esat\Http;

use Monolog\Logger;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

/**
 * Class BaseServiceTest
 * @package Esat\Http
 */
class BaseServiceTest extends Http_TestCase
{
    /**
     * @covers \Esat\Http\BaseService::__construct
     * @covers \Esat\Http\BaseService::setLogger
     * @covers \Esat\Http\BaseService::getLogger
     */
    public function testConstruct()
    {
        /** @var BaseService|MockObject $client */
        $logger = new Logger('Http');
        $client = $this->getMockForAbstractClass(BaseService::class, [$logger]);
        $this->assertTrue($client instanceof BaseService);
        $this->assertNotNull($client->getLogger());
        $this->assertEquals($logger, $client->getLogger());
        $this->assertTrue($client->getLogger() instanceof LoggerInterface);
    }
}
