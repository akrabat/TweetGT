<?php

class Application_Model_Twitter
{
    /**
     * @var array
     */
    protected $_options;
    
    /**
     * 
     * @var App_Service_Twitter
     */
    protected $_service;
    
    
    public function __construct($options)
    {
        $this->_options = $options;
    }
    
    public function getName()
    {
        try {
            $response = $this->getService()->account->verifyCredentials();
            $name = $response->name;
        } catch (Zend_Exception $e) {
            return '';
        }
        return $name;
    }
    
    public function send($message, $latitude, $longitude)
    {
        $response = $this->getService()->status->updateWithPosition($message, $latitude, $longitude);
        return $response;
    }
    
    /**
     * Connect to Twitter
     * @return Zend_Service_Twitter
     */
    public function getService()
    {
        if(!$this->_service) {
            $this->_service = new App_Service_Twitter($this->_options);
        }

        return $this->_service;
    }
    
}

