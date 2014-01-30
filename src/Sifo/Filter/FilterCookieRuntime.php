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

/**
 * This class is used to update the runtime filter with the cookies changes.
 * @author Albert Lombarte, Sergio Ambel
 */
class FilterCookieRuntime extends FilterCookie
{

    static public function setCookie( $key, $value )
    {
        self::getInstance()->request[$key] = $value;
    }

    static public function deleteCookie( $key )
    {
        unset( self::getInstance()->request[$key] );
    }
}