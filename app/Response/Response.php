<?php 

namespace App\Response;

class Response
{
    private $messages;
    private $type;
    private $dataSet;

    public function __construct() 
    {  
        $this->dataSet = [];
    }

    /**
     * Set for options messages to response object
     * @param string $data message
     */
    public function setMessages($message)
    {
        $this->messages = $message;
    }

    /**
     * gGet for options messages to response object
     * @return string $data message
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Method Set for data options with object content
     * @param string $name name to index
     * @param object $data 
     */
    public function setDataSet($name, $data)
    {
        $this->dataSet[$name] = $data;
    }

    /**
     * Method get for data options with object content
     * @return object $dataSet
     */
    public function getDataset()
    {
        return $this->dataSet;
    }

    /**
     * Set type message (S or N)
     * @param $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
    
    /**
     * Method get type message
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }
    
     /**
     * Function to converte a object with options
     * @return object $data
     */
    public function toString()
    {   
        $data = [];    
        $data['message']['text'] = $this->getMessages();
        $data['message']['type'] = $this->getType();
        
        $dataset = $this->getDataset();
        if (count($dataset) > 0 )
        {
            foreach ($dataset as $key => $value)
            {
                $data['dataset'][$key] = $value;
            }
        }       
        
        return $data;        
    }
}
?>