<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Response\Response;

class SolariumController extends Controller
{
    protected $client;

    public function __construct(\Solarium\Client $client)
    {
        $this->client = $client;
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

            return response()->json(Response::toString(true, "Sucess, Lucene connected"));
        } 
        catch (\Solarium\Exception $e)
        {
            return response()->json(Response::toString(false, $e->getMessage()), 500);
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
                
                return response()->json(Response::toString(true, "The elements were found!", ["element" => $fields]), 200);
            }
            else 
            {
                return response()->json(Response::toString(false, "No occurrences were found!"), 200);
            }
            
        }
        catch (\Solarium\Exception $e)
        {
            return response()->json(Response::toString(false, $e->getMessage()), 500);
        }
        
        
    }

    /**
     * Test to debug queries and validates results
     */
    public function debugQuery() 
    {
        /**
         * Os testes serão pra validar retornos e etc, todo echo será removido e a medida que 
         * for sendo utilizados, os metodos irão se transformar em um objeto
        */
        $query = $this->client->createSelect();
        $query->setQuery('ATL');

        // add debug settings
        $debug = $query->getDebug();
        $debug->setExplainOther(); //identificador de conjunto de documentos

        // this executes the query and returns the result
        $resultset = $this->client->select($query);
        $debugResult = $resultset->getDebug();

        // display the debug results
        echo '<h1>Debug data</h1>';
        echo 'Querystring: ' . $debugResult->getQueryString() . '<br/>';
        echo 'Parsed query: ' . $debugResult->getParsedQuery() . '<br/>';
        echo 'Query parser: ' . $debugResult->getQueryParser() . '<br/>';
        echo 'Other query: ' . $debugResult->getOtherQuery() . '<br/>';

        echo '<h2>Explain data</h2>';
        foreach ($debugResult->getExplain() as $key => $explanation) {
            echo '<h3>Document key: ' . $key . '</h3>';
            echo 'Value: ' . $explanation->getValue() . '<br/>';
            echo 'Match: ' . (($explanation->getMatch() == true) ? 'true' : 'false')  . '<br/>';
            echo 'Description: ' . $explanation->getDescription() . '<br/>';
            echo '<h4>Details</h4>';
            foreach ($explanation as $detail) {
                echo 'Value: ' . $detail->getValue() . '<br/>';
                echo 'Match: ' . (($detail->getMatch() == true) ? 'true' : 'false')  . '<br/>';
                echo 'Description: ' . $detail->getDescription() . '<br/>';
                echo '<hr/>';
            }
        }

        echo '<h2>ExplainOther data</h2>';
        foreach ($debugResult->getExplainOther() as $key => $explanation) {
            echo '<h3>Document key: ' . $key . '</h3>';
            echo 'Value: ' . $explanation->getValue() . '<br/>';
            echo 'Match: ' . (($explanation->getMatch() == true) ? 'true' : 'false')  . '<br/>';
            echo 'Description: ' . $explanation->getDescription() . '<br/>';
            echo '<h4>Details</h4>';
            foreach ($explanation as $detail) {
                echo 'Value: ' . $detail->getValue() . '<br/>';
                echo 'Match: ' . (($detail->getMatch() == true) ? 'true' : 'false')  . '<br/>';
                echo 'Description: ' . $detail->getDescription() . '<br/>';
                echo '<hr/>';
            }
        }

        echo '<h2>Timings (in ms)</h2>';
        echo 'Total time: ' . $debugResult->getTiming()->getTime() . '<br/>';
        echo '<h3>Phases</h3>';
        foreach ($debugResult->getTiming()->getPhases() as $phaseName => $phaseData) {
            echo '<h4>' . $phaseName . '</h4>';
            foreach ($phaseData as $class => $time) {
                echo $class . ': ' . $time . '<br/>';
            }
            echo '<hr/>';
        }

        die();
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

            return response()->json(Response::toString(true, "Document created!", ["result" => $doc]), 200);
        }
        catch (\Solarium\Exception $e)
        {
            return response()->json(Response::toString(false, $e->getMessage()), 500);
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

            return response()->json(Response::toString(true, "Document created!", ["result" => $doc]), 200);
        }
        catch (\Solarium\Exception $e)
        {
            return response()->json(Response::toString(false, $e->getMessage()), 500);
        }
    }
}