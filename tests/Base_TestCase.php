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

use PHPUnit\Framework\TestCase;

/**
 * Class Base_TestCase
 * @package Esat\Http
 */
class Base_TestCase extends TestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        // Set error reporting
        error_reporting(E_ALL & ~(E_NOTICE | E_WARNING | E_DEPRECATED));
    }
}
