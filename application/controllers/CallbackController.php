<?php

/**
 * Controller to handle the response from Twitter. All we need to do is store
 * the access token into the session.
 */
class CallbackController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $session = new Zend_Session_Namespace();
        $query = $this->getRequest()->getQuery();
        if (!empty($query) && isset($session->requestToken)) {

            // Get the model instance from the action helper
            $twitter = $this->_helper->twitter(); /* @var $twitter Application_Model_Twitter */

            // turn the request token into an access token
            $accessToken = $twitter->getAccessToken($query, $session->requestToken);

            // store the access token
            $session->accessToken = $accessToken;

            // we don't need the request token any more
            unset($session->requestToken);

            // redirect back to home page
            $this->_helper->redirector('index', 'index');
        } else {
            throw new Zend_Exception('Invalid callback request. Oops. Sorry.');
        }
    }

}

