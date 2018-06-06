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
            $this->setMessage($e->getMessage());
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
        /*
        // get a select query instance
        $query = $this->client->createSelect();

        // get the facetset component
        $facetSet = $query->getFacetSet();

        // create a facet field instance and set options
        $facetSet->createFacetField($stringSearch)->setField("abreviacao");

        // this executes the query and returns the result
        $resultset = $this->client->select($query);

        // display facet counts
        echo '<hr/>Facet counts for field "type":<br/>';
        $facet = $resultset->getFacetSet()->getFacet('stock');
        print_r($resultset); die();
        foreach($facet as $value => $count) {
            echo "{$value} [{$count}]<br/>"; // ex output: type name [100]
        }
        
        die();
        */
        try
        {
            $query = $this->client->createSelect();
            $query->createFilterQuery('field')->setQuery($stringSearch);
            $resultset = $this->client->select($query);

            /*
            $statsResult = $resultset->getStats();
            foreach ($statsResult as $field) {
                echo '<h1>' . $field->getName() . '</h1>';
                echo 'Min: ' . $field->getMin() . '<br/>';
                echo 'Max: ' . $field->getMax() . '<br/>';
                echo 'Sum: ' . $field->getSum() . '<br/>';
                echo 'Count: ' . $field->getCount() . '<br/>';
                echo 'Missing: ' . $field->getMissing() . '<br/>';
                echo 'SumOfSquares: ' . $field->getSumOfSquares() . '<br/>';
                echo 'Mean: ' . $field->getMean() . '<br/>';
                echo 'Stddev: ' . $field->getStddev() . '<br/>';

                echo '<h2>Field facets</h2>';
                foreach ($field->getFacets() as $field => $facet) {
                    echo '<h3>Facet ' . $field . '</h3>';
                    foreach ($facet as $facetStats) {
                        echo '<h4>Value: ' . $facetStats->getValue() . '</h4>';
                        echo 'Min: ' . $facetStats->getMin() . '<br/>';
                        echo 'Max: ' . $facetStats->getMax() . '<br/>';
                        echo 'Sum: ' . $facetStats->getSum() . '<br/>';
                        echo 'Count: ' . $facetStats->getCount() . '<br/>';
                        echo 'Missing: ' . $facetStats->getMissing() . '<br/>';
                        echo 'SumOfSquares: ' . $facetStats->getSumOfSquares() . '<br/>';
                        echo 'Mean: ' . $facetStats->getMean() . '<br/>';
                        echo 'Stddev: ' . $facetStats->getStddev() . '<br/>';
                    }
                }

                echo '<hr/>';
            }
            */
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