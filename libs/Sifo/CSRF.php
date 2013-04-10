<?php
/**
 * LICENSE
 *
 * Copyright 2013 Nil Portugués Calderó
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

/**
 // Set up example

class ExampleController extends \Sifo\Controller {

    //String explaining the intention of the request. Can be anything you like, but it's a good idea to use it to explain
    //the reason of usage in current controller is good usage.
    protected $csrf_intention = 'json_access';

    //Will be holding the CSRF class instance
    protected $csrf;

    public function __construct() {

        //Session must be started first
        $this->session = Session::getInstance();

        //Pass session to the CSRF class and generate token
        $this->csrf = CSRF::getInstance($this->session);
        $this->csrf->setCSRF($this->csrf_intention);
    }

    public function build(){

        //Example: only submit data if POST and valid CSRF
        $filter = \Sifo\Filter::getInstance();

        if($filter->isSent('csrf')) {

            $token = $filter->getString('csrf_token');
            if($this->csrf->isCsrfTokenValid($this->csrf_intention, $token)){

                // save data, for example
                //$this->setLayout etc..
            }

        }  else {
            //Do whatever you need to do
            $this->assign('csrf',$this->csrf->getToken());
            //$this->setLayout etc..
        }
    }
}
 */

namespace Sifo;


class CSRF
{
    private $csrf_token;
    private $csrf_intention;
    private $csrf_secret;
    private $session;

    // Singleton
    static private $instance;


    private function __construct(Session $session){
        $this->session = $session;
    }

    /**
     * Singleton
     *
     * @static
     * @return Session
     */
    public static function getInstance(Session $session)
    {
        if ( !isset( self::$instance ) )
        {
            self::$instance = new self($session);
        }
        return self::$instance;
    }

    /**
     * Starts Session if not already started and Generates a CSRF secret and intention tokens.
     *
     * @param $intention
     * @return CSRF
     */
    public function setCSRF($intention) {

        //Starts session if it wasn't and generates tokens
        if(!$this->session->get('csrf_intention') || !$this->session->get('csrf_secret')){

            $this->session->set('csrf_intention',base64_encode($intention));
            $this->session->set('csrf_secret',str_shuffle(md5(time().session_id())));
        }
        $this->csrf_intention = $this->session->get('csrf_intention');
        $this->csrf_secret = $this->session->get('csrf_secret');
        $this->csrf_token = $this->generateCsrfToken($this->csrf_secret, $this->csrf_intention);

        return $this;
    }

    /**
     * Retrieve the token to be used in the view.
     *
     * @return mixed
     */
    public function getToken() {
        return $this->csrf_token;
    }


    /**
     * Generates a CSRF token for a page of your application.
     *
     * @param string $csrf_secret Some random secret value.
     * @param string $intention Some value that identifies the action intention (i.e. "authenticate"). Doesn't have to be a secret value.
     */
    public function generateCsrfToken($csrf_secret,$intention) {

        return sha1($csrf_secret.$intention);
    }


    /**
     * Validates a CSRF token.
     *
     * @param string $intention The intention used when generating the CSRF token
     * @param string $token     The token supplied by the browser
     *
     * @return Boolean Whether the token supplied by the browser is correct
     */
    public function isCsrfTokenValid($intention, $token) {

        return ($token == $this->generateCsrfToken($this->csrf_secret,base64_encode($intention)));
    }
}