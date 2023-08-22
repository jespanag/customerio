<?php

namespace Customerio\Endpoint\V2;

use Customerio\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\RequestExceptionInterface;

class BaseV2
{
    /** @var Client */
    protected $client;

    /**
     * Base constructor.
     * @param $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param null $id
     * @param array $extra
     * @return string
     */
    public function singlePath($id = null, array $extra = []): string
    {
        return $this->generatePath('entity', $id, $extra);
    }

    /**
     * @param null $id
     * @param array $extra
     * @return string
     */
    public function batchPath($id = null, array $extra = []): string
    {
        return $this->generatePath('batch', $id, $extra);
    }


    /**
     * @param $prefix
     * @param null $id
     * @param array $extra
     * @return string
     */
    public function generatePath($prefix, $id = null, array $extra = []): string
    {
        $path = [
            $prefix,
        ];

        if (!empty($id)) {
            $path[] = (string)$id;
        }

        if (!empty($extra)) {
            $path = array_merge($path, $extra);
        }

        return implode('/', $path);
    }

    /**
     * @param $message
     * @param $method
     */
    protected function mockException($message, $method): RequestExceptionInterface
    {
        throw new RequestException($message, (new Request($method, '/')));
    }
}
