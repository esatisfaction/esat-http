<?php

/*
 * This file is part of the Esat Http Package.
 *
 * (c) e-satisfaction Developers <tech@e-satisfaction.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Esat\Http\Config;

use Esat\Model\Support\SingletonModel;

/**
 * Class Connection
 * @package Esat\Http\Config
 */
class Connection extends SingletonModel
{
    /**
     * @var string
     */
    protected $base_uri;

    /**
     * @var string
     */
    protected $version;

    /**
     * @param array $model
     *
     * @return SingletonModel|Connection
     */
    public static function getInstance($model = [])
    {
        return parent::getInstance($model);
    }

    /**
     * @return string
     */
    public function getBaseUri()
    {
        return $this->base_uri;
    }

    /**
     * @param string $base_uri
     *
     * @return $this
     */
    public function setBaseUri(string $base_uri)
    {
        $this->base_uri = $base_uri;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     *
     * @return $this
     */
    public function setVersion(string $version)
    {
        $this->version = $version;

        return $this;
    }
}
