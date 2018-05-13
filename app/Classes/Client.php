<?php
namespace App\Classes;
//require __DIR__.'/../../../../vendor/autoload.php';


class Client 
    {
        private $client;
        private $doc;
        private $config = [
            'endpoint' => [
                'localhost' => [
                    'host' => '127.0.0.1',
                    'port' => 8983,
                    'path' => '/solr/teste',
                ]
            ]
        ];

        public function __construct()
        {
            echo "aqui"; die();
            $this->client = new Solarium\Client($this->config);
        }

        public function search($parametro)
        {
            $query = $this->client->createSelect();
            $query->createFilterQuery('abreviacao')->setQuery($parametro);
            
            $resultset = $this->client->select($query);
            return $resultset;
        }
        
        public function addDoc($json)
        {
            /*testar via curl
            $ch = curl_init("http://127.0.0.1:8983/solr/teste/update?wt=json");
            $data = array(
                "add" => array( 
                    "doc" => array(
                        "id"   => "HW2212",
                        "title" => "Hello World 2"
                    ),
                    "commitWithin" => 1000,
                ),
            );
            $data_string = json_encode($data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            $response = curl_exec($ch);
            */
            $this->doc = new SolrInputDocument();
            foreach ($json->clubes as $key => $value) 
            {                
                foreach ($value as $key2 => $value2) 
                {
                    $this->doc->addField("'.$key2.'", "'.$value2.'");
                }
                try
                {
                    $updateResponse = $this->client->addDocument($this->doc);
                }
                catch (Execption $e) 
                {
                    return false;
                }
            }
            
            return $updateResponse->getResponse();
        }
        public function mergingDoc($docs)
        {
            $second_doc = new SolrDocument();
            $docs->addField('id', 1123);            
        }
        public function searchDocuments()
        {
            $query = new SolrQuery();
            $query->setQuery('lucene');
            $query->setStart(0);
            $query->setRows(50);
            $query->addField('cat')->addField('features')->addField('id')->addField('timestamp');
            $query_response = $client->query($query);
            $response = $query_response->getResponse();
            
            /*
             * $query = new SolrQuery();
            $query->setTerms(true);
            $query->setTermsField('cat');
            $updateResponse = $client->query($query);
            print_r($updateResponse->getResponse());
             */
            return $response;
        }
    }
?>