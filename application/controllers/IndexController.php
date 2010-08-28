<?php

class IndexController extends Zend_Controller_Action
{
    /**
     * @var Zend_Session_Namespace
     */
    protected $_session;

    /**
     * @var Application_Model_Twitter
     */
    protected $_twitter;

    public function init()
    {
        $this->_session = new Zend_Session_Namespace();

        $this->_twitter = $this->getInvokeArg('twitter');
    }

    public function indexAction()
    {
        $this->view->headTitle('Send Tweet');
        $this->view->title = 'Send Tweet';
        $fc = $this->getFrontController();

        if (isset($this->_twitter)) {
            
            $this->view->name = $this->_twitter->getName();

            // Google map
            $config = $fc->getParam('bootstrap')->getOptions();
            $this->view->mapApiKey = $config['map']['apikey'];
            $coords = $config['map']['initial'];
            if(isset($_COOKIE['position'])) {
                $coords = unserialize($_COOKIE['position']);
            }

            // Form to do tweeting with position
            $form = new Application_Form_Tweet();
            $form->setDefaults(array('latitude'=>$coords['latitude'], 'longitude'=>$coords['longitude']));
            $form->setAction($this->view->url(array(), null, true));
            $this->view->form = $form;
            if ($this->getRequest()->isPost()) {
                if ($form->isValid($this->getRequest()->getPost())) {
                    $data = $form->getValues();
                    $tweet = $data['tweet'];
                    $latitude = $data['latitude'];
                    $longitude = $data['longitude'];
                    setcookie("position", serialize(array('latitude'=>$latitude, 'longitude'=>$longitude)), time()+7776000);  // expire in 90 days
                    
                    try {
                        $result = $this->_twitter->send($tweet, $latitude, $longitude);
                        /* @var $result Zend_Rest_Client_Result */
                        if ($result->isSuccess()) {
                            $message = 'Tweet sent';
                        } else {
                            $message = 'Failed to send tweet.';
                        }
                    } catch (Exception $e){
                        $message = 'Failed to send tweet. Reported error: ' . $e->getMessage();
                    }

                    $this->_helper->flashMessenger->addMessage($message);
                    $this->_helper->redirector->gotoRouteAndExit();
                }
            }
        }
        
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }

    public function logoutAction()
    {
        $this->_session->unsetAll();
        $this->_helper->redirector('index', 'index');
    }

    public function loginAction()
    {
        if (isset($this->_session->accessToken)) {
            // clear sessions and redirect back here
            $this->_session->unsetAll();
            $this->_helper->redirector('login');
        } else {
            $consumer = $this->getFrontController()->getParam('oAuthConsumer');
            $token = $consumer->getRequestToken();
            
            $this->_session->requestToken = $token;
            $consumer->redirect(); // redirect to Twitter
        }
    }
}
