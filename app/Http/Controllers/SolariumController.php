<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    
    /**
     * Method to test a base solarium
     * @return \Illuminate\Http\Response
     */
    public function ping()
    {
        // create a ping query
        $ping = $this->client->createPing();

        // execute the ping query
        try 
        {
            $this->client->ping($ping);

            $this->response->setType('S');
            $this->setMessage('Work!');

            return response()->json($this->response->toString());
        } 
        catch (\Solarium\Exception $e) 
        {
            $this->response->setType('S');
            $this->setMessage('Error!');
            return response()->json($this->response->toString(), 500);
        }
    }

    /**
     * Function to search in a documents
     * @param string $stringSearch
     * @return \Illuminate\Http\Response
     */
    public function search($stringSearch)
    {
        
        $query = $this->client->createSelect();
        $query->createFilterQuery('abreviacao')->setQuery($stringSearch);
        $resultset = $this->client->select($query);

        if($resultset->getNumFound() > 0)
        {
            $this->response->setType("S");
            $this->response->setMessages("The elements were found!");

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
            $this->response->setMessages("No occurrences were found!");
        }
        
        return response()->json($this->response->toString());
    }

    /**
     * Test to add a document on base solarium
     * @return \Illuminate\Http\Response
     */
    public function addDocument(Request $request)
    {
        try
        {
            $update = $this->client->createUpdate();

            $objectItensDocument = $request->get('historias');

            foreach ($objectItensDocument as $key => $itens) 
            {
                // create a new document for the data
                $doc = new \stdClass();               
                
                foreach ($itens as $field => $value) 
                {
                    $doc->{$field} = $value;
                }
                
                $docAdd[] = $doc;
            }

            $update->addDocuments($docAdd);
            $update->addCommit();

            $result = $client->update($update);

            $this->response->setType("N");
            $this->response->setMessages("Documents created!");
            $this->response->setDataSet('result', $result);

            return response()->json($this->response->toString(), 200);
        }
        catch (Execption $e)
        {
            $this->response->setType("N");
            $this->response->setMessages("Document not created!");

            return response()->json($this->response->toString(), 500);
        }
        
    }
}