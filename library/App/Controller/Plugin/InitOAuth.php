<?php
class App_Controller_Plugin_InitOAuth extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Http $request)
    {
        $fc = Zend_Controller_Front::getInstance();
        $options = $fc->getParam('bootstrap')->getOptions();
                
        if(!isset($options['twitter']['consumerKey']) || !isset($options['twitter']['consumerSecret'])) {
            throw new Zend_Exception('Failed to find OAuth info for Twitter');
        }
        $url = $request->getScheme() . '://' .  $request->getHttpHost() . $request->getBaseUrl();

      
        $session = new Zend_Session_Namespace();
        
        if (isset($session->accessToken)) {
            $token = $session->accessToken;
            
            $config = array();
            $config['username'] = $token->screen_name;
            $config['accessToken'] = $token;
            $config['consumerKey'] = $options['twitter']['consumerKey'];
            $config['consumerSecret'] = $options['twitter']['consumerSecret'];
            $config['callbackUrl'] = $url . '/callback';


            $twitter = new Application_Model_Twitter($config);
            $fc->setParam('twitter', $twitter);
            $fc->getDispatcher()->setParams($fc->getParams()); // sync for use in controllers
            
        } else {

            $configuration = array(
                'version' => '1.0',
                'requestScheme' => Zend_Oauth::REQUEST_SCHEME_HEADER,
                'signatureMethod' => 'HMAC-SHA1',
                'callbackUrl' => $url . '/callback',
                'requestTokenUrl' => 'http://twitter.com/oauth/request_token',
                'authorizeUrl' => 'http://twitter.com/oauth/authorize',
                'accessTokenUrl' => 'http://twitter.com/oauth/access_token',
                'consumerKey' => $options['twitter']['consumerKey'],
                'consumerSecret' => $options['twitter']['consumerSecret']
            );

            $consumer = new Zend_Oauth_Consumer($configuration);
            $fc->setParam('oAuthConsumer', $consumer);
        }

    }
}