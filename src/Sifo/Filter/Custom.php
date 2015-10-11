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

namespace Sifo\Filter;

use Sifo\Exception\FilterException;

class Custom extends Filter
{
    /**
     * Singleton object.
     *
     * @var Filter
     */
    protected static $instance;

    /**
     * Allow creation of different objects, the FilterCustom is not based on
     * global values like $_GET or $_POST and might be used for different purposes
     * in the same execution thread.
     *
     * @param array $request
     *
     * @return Custom
     */
    public function __construct($request)
    {
        return parent::__construct($request);
    }

    /**
     * Filters variables passed in the array and empties original input.
     *
     * @throws FilterException
     * @return Filter
     */
    public static function getInstance()
    {
        $params = func_get_args();
        if ((!isset($params[0])) || (!is_array($params[0]))) {
            throw new FilterException('The variable passed inside the getInstance( $array ) method is not an array.');
        }
        $array = $params[0];
        if (!self::$instance) {
            self::$instance = new self($array);
        }

        return self::$instance;
    }
}
