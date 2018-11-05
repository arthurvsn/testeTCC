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
            $this->response->setMessages('Work!');

            return response()->json($this->response->toString());
        } 
        catch (\Solarium\Exception $e)
        {
            $this->response->setType('S');
            $this->response->setMessages($e->getMessage());
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
        try
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
                    $object = new \stdClass();
                    foreach ($document as $field => $value) 
                    {
                        
                        if (is_array($value)) 
                        {
                            $value = implode(', ', $value);
                        }

                        $object->{$field} = $value;
                    }
                
                    $fields[] = $object;
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
        catch (\Solarium\Exception $e)
        {
            $this->response->setType("N");
            $this->response->setMessages($e->getMessaage());
            return response()->json($this->response->toString(), 404);
        }
        
        
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

            $objectItensDocument = $request->get('time');
            
            // create a new document for the data            
            $doc = $update->createDocument();      

            foreach ($objectItensDocument as $field => $value) 
            {
                $doc->{$field} = $value;   
            }


            $update->addDocuments(array($doc));
            $update->addCommit();

            $result = $this->client->update($update);

            $this->response->setType("S");
            $this->response->setMessages("Documents created!");
            $this->response->setDataSet('result', $doc);

            return response()->json($this->response->toString(), 200);
        }
        catch (\Solarium\Exception $e)
        {
            $this->response->setType("N");
            $this->response->setMessages("Document not created!");

            return response()->json($this->response->toString(), 500);
        }  
    }

    /**
     * Method do update a document and override the same
     */
    public function updateDocument(Request $request)
    {
        //Ainda não tem controle de ID por falta de regra de negocio
        try
        {
            $update = $this->client->createUpdate();

            $objectItensDocument = $request->get('time');
            
            // create a new document for the data            
            $doc = $update->createDocument();      

            foreach ($objectItensDocument as $field => $value) 
            {
                $doc->{$field} = $value;   
            }


            $update->addDocuments([$doc], true);
            $update->addCommit();

            $result = $this->client->update($update);

            $this->response->setType("S");
            $this->response->setMessages("Documents created!");
            $this->response->setDataSet('result', $doc);

            return response()->json($this->response->toString(), 200);
        }
        catch (\Solarium\Exception $e)
        {
            $this->response->setType("N");
            $this->response->setMessages("Document not created!");

            return response()->json($this->response->toString(), 500);
        }
    }
}