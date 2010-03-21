<?php

class Application_Model_Twitter
{
    /**
     * @var Zend_Oauth_Consumer
     */
    protected $_oAuthConsumer;
    
    /**
     * 
     * @var Application_Service_Twitter
     */
    protected $_service;
    
    
    public function __construct(Zend_Oauth_Consumer $oAuthConsumer)
    {
        $this->_oAuthConsumer = $oAuthConsumer;
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
            $this->_service= new Application_Service_Twitter();
        }
        return $this->_service;
    }
    
}

