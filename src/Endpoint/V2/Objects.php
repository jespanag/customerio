<?php

namespace Customerio\Endpoint\V2;

use GuzzleHttp\Exception\GuzzleException;

class Objects extends Base
{

    /**
     * Objects constructor.
     * @param $client
     */
    public function __construct($client)
    {
        parent::__construct($client);
    }

    /**
     * Add new object
     * @param array $attributes
     * @param array $relationships
     * @return mixed
     * @throws GuzzleException
     */
    public function add(array $attributes, array $relationships = [])
    {
        if ( !isset($attributes['object_type_id']) || !isset($attributes['object_id']) ) {
            $this->mockException('object_type_id and object_id required!', 'POST');
        } // @codeCoverageIgnore

        $body = [
            "type" => "object", 
            "action" => "identify", 
            "cio_relationships" => [$relationships]
        ]; 

        $this->setIdentifierFromAttributes($attributes, $body);

        $body["attributes"] = $attributes;

        // die(json_encode($body));

        return $this->client->post($this->singlePath(), $body);
    }

    /**
     * Set the request body with the relevant identifier of the object
     * @param array $attributes
     * @param array $body original request body
     * @return void 
     */
    private function setIdentifierFromAttributes(array &$attributes, array &$body):void
    {
        
        $body["identifiers"] = [
            "object_type_id" => $attributes["object_type_id"],
            "object_id" => $attributes["object_id"]
        ];

        unset($attributes["object_type_id"]);
        unset($attributes["object_id"]);
    }
}
