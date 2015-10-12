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
namespace Sifo;

use Sifo\Filter\CookieRuntime;

class Cookie
{
    protected static $cookies;
    private static $domain;
    private static $path;

    private static function _initDomain()
    {
        self::$cookies = array();
        // Take domain from configuration to allow multiple subdomain compatibility with cookies.
        self::$domain = Domains::getInstance()->getDomain();
        self::$path = '/';
    }

    public static function set($name, $value, $days = 14, $domain = false)
    {
        $domain ?: self::_initDomain();

        if (0 == $days) {
            $result = setcookie($name, $value, 0, self::$path, self::$domain);
        } else {
            $result = setcookie($name, $value, time() + (86400 * $days), self::$path, self::$domain);
        }
        if (!$result) {
            trigger_error("COOKIE WRITE FAIL: Tried to write '$name' with value '$value' but failed.");

            return false;
        }

        // Filter runtime update:
        CookieRuntime::setCookie($name, $value);

        return true;
    }

    public static function delete($name)
    {
        self::_initDomain();
        $result = setcookie($name, '', time() - 3600, self::$path, self::$domain);
        if (!$result) {
            trigger_error("COOKIE DELETE FAIL: Tried to delete '$name' but failed.");

            return false;
        }

        // Filter runtime update:
        CookieRuntime::deleteCookie($name);

        return true;
    }

    /**
     * Read one (string) or several (array) cookies and returns it with a simple sanitization of the content.
     *
     * @deprecated The Cookie::get from Filter\Cookie::getString.
     *
     * @param string|array $cookie
     *
     * @return string|false
     */
    public static function get($cookies)
    {
        trigger_error("'Cookie::get' is deprecated, please use 'Sifo\\Filter\\Cookie'. Ex: \\Sifo\\Filter\\Cookie::getInstance()->getString( 'cookie_key' );");

        if (is_array($cookies)) {
            foreach ($cookies as $cookie) {
                $values[$cookie] = self::_sanitizeCookie($cookie);
            }

            if (!isset($values)) {
                return false;
            }

            return $values;
        } else {
            return self::_sanitizeCookie($cookies);
        }
    }

    /**
     * Returns a sanitized Cookie.
     *
     * @param array $cookies
     *
     * @return string|false
     */
    private static function _sanitizeCookie($cookie)
    {
        if (Filter\Cookie::getInstance()->isSent($cookie)) {
            return Filter\Cookie::getInstance()->getString($cookie);
        }

        return false;
    }
}
