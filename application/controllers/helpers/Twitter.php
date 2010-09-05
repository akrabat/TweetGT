<?php

class Application_Controller_Helper_Twitter extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var Application_Model_Twitter
     */
    protected $_twitter;

    public function direct()
    {
        if (!$this->_twitter) {
            $controller = $this->getActionController();

            $config = array();

            $session = new Zend_Session_Namespace();
            if ($session->accessToken) {
                $token = $session->accessToken;
                $config['username'] = $token->screen_name;
                $config['accessToken'] = $token;
            }
            
            $options = $controller->getInvokeArg('bootstrap')->getOptions();
            $config['consumerKey'] = $options['twitter']['consumerKey'];
            $config['consumerSecret'] = $options['twitter']['consumerSecret'];

            $request = $controller->getRequest();
            $url = $request->getScheme() . '://' .  $request->getHttpHost() . $request->getBaseUrl();
            $config['callbackUrl'] = $url . '/callback';

            $this->_twitter = new Application_Model_Twitter($config);
        }

        return $this->_twitter;
    }

}

