<?php

/*
 * This file is part of the e-satisfaction.com Http Package.
 *
 * (c) e-satisfaction.com Developers <tech@e-satisfaction.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Esat\Http\Multipart;

use Esat\Helpers\UuidHelper;
use Esat\Http\Base_TestCase;
use Exception;

/**
 * Class FileTest
 * @package Esat\Http\Multipart
 */
class FileTest extends Base_TestCase
{
    /**
     * @covers \Esat\Http\Multipart\File::setName
     * @covers \Esat\Http\Multipart\File::getName
     * @covers \Esat\Http\Multipart\File::setFilename
     * @covers \Esat\Http\Multipart\File::getFilename
     * @covers \Esat\Http\Multipart\File::setContents
     * @covers \Esat\Http\Multipart\File::getContents
     * @covers \Esat\Http\Multipart\File::setHeaders
     * @covers \Esat\Http\Multipart\File::getHeaders
     *
     * @throws Exception
     */
    public function testGetterAndSetter()
    {
        // Get instance
        $model = new File();

        // Name
        $value = UuidHelper::create();
        $this->assertTrue($model->setName($value) instanceof File);
        $this->assertEquals($value, $model->getName());

        // File Name
        $value = UuidHelper::create();
        $this->assertTrue($model->setFilename($value) instanceof File);
        $this->assertEquals($value, $model->getFilename());

        // Contents
        $value = UuidHelper::create();
        $this->assertTrue($model->setContents($value) instanceof File);
        $this->assertEquals($value, $model->getContents());

        // Headers
        $value = [
            'name' => 'value',
        ];
        $this->assertTrue($model->setHeaders($value) instanceof File);
        $this->assertEquals($value, $model->getHeaders());
    }
}
