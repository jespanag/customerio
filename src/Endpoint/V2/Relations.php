<?php

namespace Customerio\Endpoint\V2;

use GuzzleHttp\Exception\GuzzleException;

class Relations extends Base
{

    /**
     * Relation constructor.
     * @param $client
     */
    public function __construct($client)
    {
        parent::__construct($client);
    }

     /**
     * Create a relation
     * @param string $id
     * @return mixed
     * @throws GuzzleException
     */

    public function createRelationFromID(string $id)
    {
        $body = [
            "identifiers" => 
                ["id" => $id]
        ]; 

        return $body;
    }

     /**
     * Create a relation
     * @param string $email
     * @return mixed
     * @throws GuzzleException
     */
    
    public function createRelationFromMail(string $email)
    {
        $body = [
            "identifiers" => 
                ["email" => $email]
        ]; 

        return $body;
    }

     /**
     * Create a relation
     * @param string $id
     * @return mixed
     * @throws GuzzleException
     */
    
    public function createAnonymousRelation(string $id)
    {
        $body = [
            "identifiers" => 
                ["anonymous_id" => $id]
        ]; 

        return $body;
    }

}
