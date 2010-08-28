<?php

class CallbackController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $session = new Zend_Session_Namespace();
        if (!empty($_GET) && isset($session->requestToken)) {
            $consumer = $this->getFrontController()->getParam('oAuthConsumer');
            /* @var $consumer Zend_Oauth_Consumer */
            $token = $consumer->getAccessToken($_GET, $session->requestToken);
            unset($session->requestToken);

            $session->accessToken = $token;
            $this->_helper->redirector('index', 'index');
        } else {
            throw new Zend_Exception('Invalid callback request. Oops. Sorry.');
        }
    }

}

