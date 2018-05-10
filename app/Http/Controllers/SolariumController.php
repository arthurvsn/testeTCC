<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Client;

class SolariumController extends Controller
{
    private $response;
    private $client;

    public function __construct() 
    {
        $this->client = new Client();
        $this->response = new Response();
    }

    public function search($stringSearch) 
    {

        $resultQuery = $this->client->search($stringSearch);
        
        $this->response->setType("S");
        $this->response->setMessages("Search sucessufuly");
        $this->response->setDataSet("Result", $resultQuery);

        return response()->json($this->response->toString(), 200);

    }
    
}
