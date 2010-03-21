<?php

class IndexController extends Zend_Controller_Action
{
    /**
     * @var Zend_Session_Namespace
     */
    protected $_session;
    
    public function init()
    {
        $this->_session = new Zend_Session_Namespace();
    }

    public function indexAction()
    {
        $this->view->headTitle('Send Tweet');
        $this->view->title = 'Send Tweet';
        $fc = $this->getFrontController();

        if (isset($this->_session->accessToken)) {
            $twitter = new Application_Model_Twitter($fc->getParam('oAuthConsumer'));
            $this->view->name = $this->_session->name;
            
            $config = $fc->getParam('bootstrap')->getOptions();
            $this->view->mapApiKey = $config['map']['apikey'];
            $coords = $config['map']['initial'];
            if(isset($_COOKIE['position'])) {
                $coords = unserialize($_COOKIE['position']);
            }
            
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
                        $result = $twitter->send($tweet, $latitude, $longitude);
                        $message = 'Tweet sent';
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
        if (!isset($this->_session->accessToken)) {
            $consumer = $this->getFrontController()->getParam('oAuthConsumer');
            $token = $consumer->getRequestToken();
            
            $this->_session->requestToken = $token;
            $consumer->redirect();
        }
    }
}





