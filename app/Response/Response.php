<?php

namespace App\Response;

final class Response
{
    /**
     * 
     */
    public static function createDataSet()
    {

    }

    /**
     * Return a object to response
     * @param bool $type
     * @param string $message
     * @param array $dataset
     */
    public static function toString($type, $message, $dataset = [])
    {
        $data = [];
        
        if ($message == "")
        {
            if ($type)
            {
                $message = "Successful request";
            }
            else
            {
                $message = "Error, could not complete your request";
            }
        }

        $data['menssagem'] = $message;
        $data['status'] = $type;
        
        if(isset($dataset) && count($dataset) > 0)
        {
            foreach ($dataset as $key => $value)
            {
                $data['data'][$key] = $value;
            }
        }
        return $data;
    }
}