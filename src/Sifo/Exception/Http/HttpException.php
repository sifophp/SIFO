<?php
/**
 * LICENSE
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
 *
 */

namespace Sifo\Exception\Http;

use Exception;
use Sifo\Headers;

class HttpException extends \Exception
{
    /**
     * HTTP code used for this exception.
     *
     * @var integer
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
     * @var boolean
     */
    public $redirect = false;

    /**
     * Set the correct status code on Exception invokation.
     */
    public function __construct($message = null, $code = 0)
    {
        // Invoke parent to ensure all available data has been properly assigned:
        parent::__construct($message, $code);

        $current_exception      = get_class($this);
        $current_exception_code = ( int )str_replace(__NAMESPACE__ . '\\Exception_', '', $current_exception);

        // See if the http status code needs a redirection:
        if ((300 <= $current_exception_code) && (307 >= $current_exception_code)) {
            $this->redirect = true;
        }

        if (isset(Headers::$http_codes[$current_exception_code])) {
            $this->http_code     = $current_exception_code;
            $this->http_code_msg = Headers::$http_codes[$current_exception_code];
        } else {
            // The passed exception is not in the list. Pass a 500 error.
            $this->http_code     = 500;
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

    /**
     * Raises a Sifo exceptions based on the given HTTP status code
     *
     * @param <type> $message Reason
     * @param <type> $code HTTP status code
     */
    public static function raise($message, $code)
    {
        if (isset(Headers::$http_codes[$code])) {
            $exception = '\Sifo\Exception\Http\Exception' . $code;
            throw new $exception($message);
        } else {
            // Unknown status code.
            throw new Exception500($message, $code);
        }
    }
}
