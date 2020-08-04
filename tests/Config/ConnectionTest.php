<?php

/*
 * This file is part of the e-satisfaction.com Http Package.
 *
 * (c) e-satisfaction.com Developers <tech@e-satisfaction.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Esat\Http\Config;

use Esat\Helpers\UuidHelper;
use Esat\Http\Base_TestCase;
use Exception;

/**
 * Class ConnectionTest
 * @package Esat\Http\Config
 */
class ConnectionTest extends Base_TestCase
{
    /**
     * @covers \Esat\Http\Config\Connection::setBaseUri
     * @covers \Esat\Http\Config\Connection::getBaseUri
     * @covers \Esat\Http\Config\Connection::setVersion
     * @covers \Esat\Http\Config\Connection::getVersion
     *
     * @throws Exception
     */
    public function testGetterAndSetter()
    {
        // Get instance
        $instance = Connection::getInstance();

        // Base uri
        $value = UuidHelper::create();
        $this->assertTrue($instance->setBaseUri($value) instanceof Connection);
        $this->assertEquals($value, $instance->getBaseUri());

        // Version
        $value = UuidHelper::create();
        $this->assertTrue($instance->setVersion($value) instanceof Connection);
        $this->assertEquals($value, $instance->getVersion());
    }
}
