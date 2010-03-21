<?php

class CallbackController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $session = new Zend_Session_Namespace();
        if (!empty($_GET) && isset($session->requestToken)) {
            $consumer = $this->getFrontController()->getParam('oAuthConsumer');
            $token = $consumer->getAccessToken($_GET, $session->requestToken);
            $session->accessToken = $token;
            unset($session->requestToken);
            $this->_helper->redirector('index', 'index');
        } else {
            throw new Zend_Exception('Invalid callback request. Oops. Sorry.');
        }
    }

}

