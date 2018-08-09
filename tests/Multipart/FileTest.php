<?php

/*
 * This file is part of the Esat Http Package.
 *
 * (c) e-satisfaction Developers <tech@e-satisfaction.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Esat\Http\Multipart;

use Esat\Http\Base_TestCase;

/**
 * Class FileTest
 * @package Esat\Http\Multipart
 */
class FileTest extends Base_TestCase
{
    /**
     * @var File
     */
    private $file;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->file = new File();
    }

    /**
     * Test setting the model properties and converting to array
     * @throws \ReflectionException
     */
    public function testFile()
    {
        // Create parameters
        $name = 'name';
        $fileName = 'file_name';
        $fileContents = 'file_contents';

        // Set file parameters
        $this->file
            ->setName($name)
            ->setFilename($fileName)
            ->setContents($fileContents);

        // Assert
        $array = $this->file->toArray();
        $this->assertEquals($name, $array['name']);
        $this->assertEquals($fileName, $array['filename']);
        $this->assertEquals($fileContents, $array['contents']);
    }
}
