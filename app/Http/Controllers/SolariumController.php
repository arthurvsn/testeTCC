<?php

namespace App\Http\Controllers;

use \App\Response\Response;

class SolariumController extends Controller
{
    protected $client;
    protected $response;

    public function __construct(\Solarium\Client $client)
    {
        $this->client = $client;
        $this->response = new Response();
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

    /**
     * Function to search in a documents
     * @param string $stringSearch
     * @return Response
     */
    public function search($stringSearch)
    {
        
        $query = $this->client->createSelect();
        $query->createFilterQuery('abreviacao')->setQuery($stringSearch);
        $resultset = $this->client->select($query);

        if($resultset->getNumFound() > 0)
        {
            $this->response->setType("S");
            $this->response->setMessages("Elemento founded!");

            foreach ($resultset as $document) 
            {
                $fields[] = [
                    'id' => $document->id,
                    'nome'=> $document->nome,
                    'abreviacao'=> $document->abreviacao,
                    'pais'=> $document->pais,
                ];
            }
            
            $this->response->setDataSet("Element", $fields);
        }
        else 
        {
            $this->response->setType("N");
            $this->response->setMessages("Not Found!");
        }
        
        return response()->json($this->response->toString());
    }

    public function addDocument() 
    {

    }
}