<?php

namespace Sifo\Http;

use Sifo\Http;
use Sifo\Http\Filter\FilterCookie;

class Cookie
{

    static protected $cookies;
    static private $domain;
    static private $path;

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

        Http\FilterCookieRuntime::setCookie($name, $value);

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
        Http\FilterCookieRuntime::deleteCookie($name);

        return true;
    }

    /**
     * Read one (string) or several (array) cookies and returns it with a simple sanitization of the content.
     *
     * @deprecated The Cookie::get from FilterCookie::getString.
     *
     * @param string|array $cookie
     *
     * @return string|false
     */
    static public function get($cookies)
    {
        trigger_error("'Cookie::get' is deprecated, please use 'FilterCookie'. Ex: FilterCookie::getInstance()->getString( 'cookie_key' );");

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
    static private function _sanitizeCookie($cookie)
    {
        if (FilterCookie::getInstance()->isSent($cookie)) {
            return FilterCookie::getInstance()->getString($cookie);
        }

        return false;
    }
}
