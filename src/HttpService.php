<?php

/*
 * This file is part of the Esat Http Package.
 *
 * (c) e-satisfaction Developers <tech@e-satisfaction.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Esat\Http;

use Esat\Helpers\LoggerHelper;
use Esat\Helpers\UuidHelper;
use Esat\Http\Clients\HttpClientInterface;
use Esat\Http\Config\Connection;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Monolog\Logger;
use Psr\Cache\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Class ModelService
 * @package Esat\Support\Services
 */
abstract class HttpService extends ModelService
{
    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ResponseInterface
     */
    protected $lastResponse;

    /**
     * @var string
     */
    protected static $processId;

    /**
     * @var bool
     */
    protected $cacheEnabled = true;

    /**
     * @var CacheInterface
     */
    protected static $cache;

    /**
     * HttpService constructor.
     *
     * @param Connection          $connection
     * @param HttpClientInterface $httpClient
     * @param LoggerInterface     $logger
     * @param CacheInterface      $cache
     */
    public function __construct(Connection $connection, HttpClientInterface $httpClient, LoggerInterface $logger, CacheInterface $cache = null)
    {
        parent::__construct($logger);
        $this->connection = $connection;
        $this->httpClient = $httpClient;
        self::$cache = $cache ?? self::$cache ?? new ArrayAdapter();
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $headers
     * @param array  $parameters
     * @param array  $multipart
     *
     * @return ResponseInterface
     * @throws Exception
     */
    protected function send($method, $uri, $headers = [], $parameters = [], $multipart = [])
    {
        try {
            // Add domain and version to uri
            $serviceUri = $this->getApiUri($uri);
            $key = md5(sprintf('%s:%s', $serviceUri, json_encode($parameters)));

            // Check for cache
            if ($method == Request::METHOD_GET
                && $this->isCacheEnabled()
                && !empty($this->getFromCache($key))
            ) {
                $response = $this->getFromCache($key);
            } else {
                // Log and send request
                $this->getLogger()->debug(sprintf('%s: %s - %s', $this->getProcessId(), $method, $serviceUri));
                $response = $this->getHttpClient()->send($method, $serviceUri, $headers, $parameters, $multipart);
            }

            // Set last response
            $this->setLastResponse($response);

            // Update cache
            if ($method == Request::METHOD_GET) {
                $this->addToCache($key, $response);
                $this->setCacheEnabled(true);
            } else {
                $this->setCacheEnabled(false);
            }

            // Rewind response to set it available for reading
            $this->getLastResponse()->getBody()->rewind();
        } catch (ServerException $ex) {
            /**
             * The Client will throw a ServerException.
             * if the response has status code 5xx.
             * We log all these errors to keep track.
             */
            LoggerHelper::logThrowable($this->getLogger(), $ex, $level = Logger::ERROR, $logTrace = true);

            // Return the normal response
            $this->setLastResponse($ex->getResponse());
        } catch (ClientException $ex) {
            /**
             * The Client will throw a ClientException
             * if the response has status code 4xx.
             * We do not log these errors because they are human-generated.
             */

            // Return the normal response
            $this->setLastResponse($ex->getResponse());
        } catch (RuntimeException $e) {
        }

        // Set response and return it
        return $this->getLastResponse();
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    protected function getApiUri($uri)
    {
        $baseUri = $this->getConnection()->getBaseUri();
        $version = $this->getConnection()->getVersion();

        return $baseUri . '/' . ($version ? 'v' . $version . '/' : '') . trim($uri, '/');
    }

    /**
     * @param ResponseInterface $response
     * @param bool              $save
     *
     * @return array
     * @throws Exception
     */
    protected function getResponseAsArray(ResponseInterface $response, $save = true)
    {
        if ($save) {
            $this->setLastResponse($response);
        }

        return parent::getResponseAsArray($response);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return string
     * @throws Exception
     */
    public function getErrorFromLastResponse(ResponseInterface $response = null)
    {
        // Normalize response
        $response = $response ?: $this->getLastResponse();

        // Decode response body
        $responseBody = json_decode($response->getBody()->getContents(), true);

        // Return message or error
        return $responseBody['error'] ?: $responseBody['message'];
    }

    /**
     * @return ResponseInterface
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * @param ResponseInterface $lastResponse
     *
     * @return $this
     */
    public function setLastResponse(ResponseInterface $lastResponse)
    {
        $this->lastResponse = $lastResponse;

        return $this;
    }

    /**
     * @return HttpClientInterface
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getProcessId()
    {
        return self::$processId = self::$processId ?: UuidHelper::create();
    }

    /**
     * @return bool
     */
    public function isCacheEnabled()
    {
        return $this->cacheEnabled;
    }

    /**
     * @param bool $cacheEnabled
     *
     * @return $this
     */
    public function setCacheEnabled(bool $cacheEnabled)
    {
        $this->cacheEnabled = $cacheEnabled;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return ResponseInterface
     */
    public function getFromCache($key)
    {
        try {
            // Normalize cache key
            $key = $this->normalizeCacheKey($key);

            // Get response from cache
            return $this->getResponseFromArray(self::getCache()->get($key, function () {
                return [];
            }));
        } catch (InvalidArgumentException $exx) {
            return null;
        }
    }

    /**
     * @param string            $key
     * @param ResponseInterface $response
     * @param int               $ttl
     *
     * @return $this
     */
    public function addToCache($key, $response, $ttl = 0)
    {
        try {
            // Normalize cache key
            $key = $this->normalizeCacheKey($key);

            // Create or find item
            $item = self::getCache()->getItem($key);

            // Update item value
            $item->set($this->getResponseToArray($response));

            // Set ttl, if given
            if (!empty($ttl)) {
                $item->expiresAfter($ttl);
            }

            // Update cache
            self::getCache()->save($item);
        } catch (InvalidArgumentException $exx) {
        } catch (RuntimeException $exx) {
        }

        return $this;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return array
     * @throws RuntimeException
     */
    private function getResponseToArray($response)
    {
        return [
            'status_code' => $response->getStatusCode(),
            'headers' => $response->getHeaders(),
            'body_contents' => $response->getBody()->getContents(),
        ];
    }

    /**
     * @param array $array
     *
     * @return ResponseInterface
     */
    private function getResponseFromArray($array)
    {
        if (empty($array)) {
            return null;
        }

        return new GuzzleResponse($array['status_code'], $array['headers'], $array['body_contents']);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function normalizeCacheKey($key)
    {
        $key = str_replace('/', '_', $key);
        $key = str_replace(':', '_', $key);
        $key = str_replace('@', '_', $key);

        return $key;
    }

    /**
     * @return CacheInterface|ArrayAdapter
     */
    public static function getCache()
    {
        return self::$cache;
    }

    /**
     * @param CacheInterface $cache
     */
    public static function setCache(CacheInterface $cache)
    {
        self::$cache = $cache;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param Connection $connection
     *
     * @return $this
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;

        return $this;
    }
}
