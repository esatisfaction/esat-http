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

use Esat\Model\BaseModel;
use Exception;
use InvalidArgumentException;
use Panda\Support\Helpers\ArrayHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use ReflectionException;
use RuntimeException;
use Throwable;

/**
 * Class ModelService
 * @package Esat\Http
 */
abstract class ModelService extends BaseService
{
    /**
     * @var BaseModel
     */
    protected $model;

    /**
     * Initialize the service model object.
     */
    abstract public function initModel();

    /**
     * ModelService constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($logger);
        $this->initModel();
    }

    /**
     * @param array  $parameters
     * @param string $operation
     * @param bool   $updateParameters
     *
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function initModelWithParameters(&$parameters, $operation = '', $updateParameters = true)
    {
        // Initialize model
        $this->initModel();

        // Set parameters
        $this->updateModelWithParameters($parameters, $operation, $updateParameters);
    }

    /**
     * @param array  $parameters
     * @param string $operation
     * @param bool   $updateParameters
     *
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function updateModelWithParameters(&$parameters, $operation = '', $updateParameters = true)
    {
        // Check model
        $this->checkModel($operation);

        // Load parameters to model
        $this->getModel()->loadFromArray($parameters);

        // Update parameters
        $parameters = $updateParameters ? $this->getModel()->toArrayExtended($parameters) : $parameters;
    }

    /**
     * @param ResponseInterface $response
     * @param BaseModel         $model
     * @param string|null       $subField
     * @param bool              $clearModel
     *
     * @return bool
     * @throws Exception
     */
    public function setModelFromResponse(ResponseInterface $response, &$model = null, $subField = null, $clearModel = false)
    {
        try {
            // Get response as array
            $array = $this->getResponseAsArray($response);

            return $this->setModelFromArray($array, $model, $subField, $clearModel);
        } catch (Throwable $ex) {
            throw new Exception('An error occurred while setting the model from the given response.', 0, $ex);
        }
    }

    /**
     * @param ResponseInterface $response
     *
     * @return array
     * @throws Exception
     */
    public function getResponseAsArray(ResponseInterface $response)
    {
        try {
            // Rewind stream to make sure that response contents are available for read
            $response->getBody()->rewind();
            $contents = $response->getBody()->getContents();

            // Rewind the body again to allow future reads
            $response->getBody()->rewind();

            // Decode contents
            $result = json_decode($contents, true);
            if (is_null($result)) {
                throw new RuntimeException('Json could not be converted to array.');
            }

            return $result;
        } catch (RuntimeException $ex) {
            throw new Exception('The given response does not have the proper format.', 0, $ex);
        }
    }

    /**
     * @param array       $array
     * @param BaseModel   $model
     * @param string|null $subField
     * @param bool        $clearModel
     *
     * @return bool
     * @throws Exception
     */
    public function setModelFromArray(array $array, &$model = null, $subField = null, $clearModel = false)
    {
        try {
            // Normalize model
            $model = $model ?: $this->getModel();

            // Clear model if necessary
            if ($clearModel || empty($model)) {
                $this->clearModel();
                $this->initModel();
                $model = $this->getModel();
            }

            // Get sub-field, if given
            if (!empty($subField)) {
                $array = ArrayHelper::get($array, $subField, [], true);
            }

            // Load model from response
            if ($array && is_array($array)) {
                $model->loadFromArray($array);

                return true;
            }

            throw new Exception('Unable to read the array for loading in the model.');
        } catch (Throwable $ex) {
            throw new Exception('An error occurred while setting the model from the given array.', 0, $ex);
        }
    }

    /**
     * @return BaseModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param BaseModel $model
     *
     * @return $this
     */
    public function setModel(BaseModel $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * It clears the model and creates a new instance.
     * This function is alias for initModel().
     */
    public function clearModel()
    {
        return $this->model = null;
    }

    /**
     * @param string $operation
     *
     * @throws InvalidArgumentException
     */
    public function checkModel($operation = '')
    {
        if (!$this->getModel()) {
            throw new InvalidArgumentException(sprintf('The Service Model is not set for the required operation [%s::%s].', get_class($this), $operation));
        }
    }
}
