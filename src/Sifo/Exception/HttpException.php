<?php

/**
 * LICENSE.
 *
 * Copyright 2010 Albert Lombarte
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
 */
namespace Sifo\Exception;

use Exception;
use Sifo\Headers;

class HttpException extends \Exception
{
    /**
     * HTTP code used for this exception.
     *
     * @var int
     */
    public $http_code = 302;

    /**
     * HTTP code explanation.
     *
     * @var string
     */
    public $http_code_msg = '';

    /**
     * Whether the status code requires a redirection or not.
     *
     * @var bool
     */
    public $redirect = false;

    /**
     * Set the correct status code on Exception invokation.
     */
    public function __construct($message = null, $http_status_code = 0)
    {
        // Invoke parent to ensure all available data has been properly assigned:
        parent::__construct($message, $http_status_code);

        if (0 != $http_status_code) {
            $this->http_code = $http_status_code;
        }

        // See if the http status code needs a redirection:
        if ((300 <=  $this->http_code) && (307 >= $this->http_code)) {
            $this->redirect = true;
        }

        if (isset(Headers::$http_codes[ $this->http_code])) {
            $this->http_code_msg = Headers::$http_codes[$this->http_code];
        } else {
            // The passed exception is not in the list. Pass a 500 error.
            $this->http_code = 500;
            $this->http_code_msg = 'Internal Server Error';
        }

        // Set internal exception vars if they are empty (non declared in constructor).
        // This allows usage of methods as $e->getMessage() or $e->getCode()
        if (0 == $this->code) {
            $this->code = $this->http_code;
        }

        if (null === $this->message) {
            $this->message = $this->http_code_msg;
        }
    }
}
