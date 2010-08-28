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
        $this->_twitter = $this->_helper->twitter();
//
//
//        if (isset($this->_session->accessToken)) {
//            // we have authenticated with Twitter, so create an instance
//            // of our model (Application_Model_Twitter). Note that we need
//            // to pass in the same parameters as we did to create the
//            // consumer
//
//            $options = $this->getInvokeArg('bootstrap')->getOptions();
//            $request = $this->getRequest();
//            $token = $session->accessToken;
//            $url = $request->getScheme() . '://' .  $request->getHttpHost() . $request->getBaseUrl();
//
//            $config = array();
//            $config['username'] = $token->screen_name;
//            $config['accessToken'] = $token;
//            $config['consumerKey'] = $options['twitter']['consumerKey'];
//            $config['consumerSecret'] = $options['twitter']['consumerSecret'];
//            $config['callbackUrl'] = $url . '/callback';
//
//            $this->_twitter = new Application_Model_Twitter($config);
//        }

    }

    public function indexAction()
    {
        $this->view->headTitle('Send Tweet');
        $this->view->title = 'Send Tweet';
        $fc = $this->getFrontController();

        if ($this->_twitter->isLoggedIn()) {
            
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
        $this->_session->requestToken = $this->_twitter->getRequestToken();
        $this->_twitter->loginViaTwitterSite();


        if (isset($this->_session->accessToken)) {
            // clear sessions and redirect back here
            $this->_session->unsetAll();
            $this->_helper->redirector('login');
        } else {
            // utilise the OAuth consumer within the Twitter model to redirect
            $twitter = $this->_helper->twitter();
            $twitter->loginViaTwitterSite(); // redirect to Twitter


            $options = $this->getInvokeArg('bootstrap')->getOptions();
            $request = $this->getRequest();
            $token = $session->accessToken;
            $url = $request->getScheme() . '://' .  $request->getHttpHost() . $request->getBaseUrl();
            
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
            $token = $consumer->getRequestToken();
            
            $this->_session->requestToken = $token;
            $consumer->redirect(); // redirect to Twitter
        }
    }
}
