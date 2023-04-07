<?php

namespace Controller;

class BaseController
{
    /** 
     * Used to handle requests to undefined endpoints.
     *
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments)
    {
        $this->sendOutput('', array('HTTP/1.1 404 Not Found'));
    }
    
    /** 
     * Get querystring params. 
     * 
     * @return array 
     */
    protected function getQueryStringParams()
    {
        parse_str($_SERVER['QUERY_STRING'], $result);
        return $result;
    }

    /** 
     * Send API output. 
     * 
     * @param mixed $data 
     * @param array $httpHeader 
     */
    protected function sendOutput($data, $httpHeaders=array())
    {
        header_remove('Set-Cookie');
        if (is_array($httpHeaders) && count($httpHeaders)) {
            foreach ($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        }
        echo $data;
        exit;
    }
}