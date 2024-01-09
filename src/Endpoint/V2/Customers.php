<?php

namespace Customerio\Endpoint\V2;

use GuzzleHttp\Exception\GuzzleException;

class Customers extends Base
{
    /**
     * Customers constructor.
     * @param $client
     */
    public function __construct($client)
    {
        parent::__construct($client);
    }

    /**
     * Add new customer
     * @param array $attributes
     * @param array $relationships
     * @return mixed
     * @throws GuzzleException
     */
    public function add(array $attributes, array $relationships = [])
    {
        if (!isset($attributes['id']) && !isset($attributes['email']) && !isset($attributes['cio_id'])) {
            $this->mockException('User id or email is required!', 'POST');
        } // @codeCoverageIgnore

        $body = [
            "type" => "person", 
            "action" => "identify", 
            "cio_relationships" => $relationships
        ]; 

        $this->setIdentifierFromAttributes($attributes, $body);

        $body["attributes"] = $attributes;

        return $this->client->post($this->singlePath(), $body);
    }


   
    /**
     * Set the request body with the relevant identifier of the customer
     * @param array $attributes
     * @param array $body original request body
     * @return void 
     */
    private function setIdentifierFromAttributes(array &$attributes, array &$body):void
    {
        $customerIdentifierProperty = isset($attributes['cio_id']) ? 'cio_id' : (isset($attributes['id']) ? 'id' : 'email');

        $body["identifiers"] = [
            $customerIdentifierProperty => $attributes[$customerIdentifierProperty]
        ];

        unset($attributes[$customerIdentifierProperty]);

    }

    public function event(string $name, array $attributes)
    {
        if (!isset($attributes['id']) && !isset($attributes['email']) && !isset($attributes['cio_id'])) {
            $this->mockException('User id or email is required!', 'POST');
        } // @codeCoverageIgnore

        if (!isset($name)) {
            $this->mockException('Event name required!', 'POST');
        } // @codeCoverageIgnore

        $body = [
            "type" => "person",
            "action" => "event",
        ];

        $this->setIdentifierFromAttributes($attributes, $body);

        $body["name"] = $name;
        $body["attributes"] = $attributes;

        return $this->client->post($this->singlePath(), $body);
    }
}
