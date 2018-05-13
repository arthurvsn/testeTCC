<?php

namespace App\Http\Controllers;
use \App\Response\Response;

class SolariumController extends Controller
{
    protected $client;

    public function __construct(\Solarium\Client $client)
    {
        $this->client = $client;
    }

    public function ping()
    {
        // create a ping query
        $ping = $this->client->createPing();

        // execute the ping query
        try {
            $this->client->ping($ping);
            return response()->json('OK');
        } catch (\Solarium\Exception $e) {
            return response()->json('ERROR', 500);
        }
    }

    public function search($stringSearch)
    {
        $query = $this->client->createSelect();
        $query->createFilterQuery('abreviacao')->setQuery($stringSearch);
        $resulset = $this->client->select($query);

        // display the total number of documents found by solr
        echo "<pre>";
        var_dump($resulset); die();

    }
}