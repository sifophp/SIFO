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

namespace Sifo;

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

class Cookie
{

	static protected $cookies;
	static private $domain;
	static private $path;

	static private function _initDomain()
	{
		self::$cookies = array( );
		// Take domain from configuration to allow multiple subdomain compatibility with cookies.
		self::$domain = Domains::getInstance()->getDomain();
		self::$path = '/';
	}

	static public function set( $name, $value, $days = 14, $domain = false, $secure = false, $httpOnly = false, string $samesite = null )
	{
		$domain ?: self::_initDomain();

		$expires = 0 == $days ? 0 : time() + ( 86400 * $days );

		$result = static::setCookie( $name, $value, $expires, self::$path, self::$domain, $secure, $httpOnly, $samesite );

		if ( !$result )
		{
			trigger_error( "COOKIE WRITE FAIL: Tried to write '$name' with value '$value' but failed." );
			return false;
		}

		// Filter runtime update:
		FilterCookieRuntime::setCookie( $name, $value );

		return true;
	}

	static public function delete( $name )
	{
		self::_initDomain();
		$result = static::setCookie( $name, '', time() - 3600, self::$path, self::$domain);
		if ( !$result )
		{
			trigger_error( "COOKIE DELETE FAIL: Tried to delete '$name' but failed." );
			return false;
		}

		// Filter runtime update:
		FilterCookieRuntime::deleteCookie( $name );

		return true;
	}

	/**
	 * Read one (string) or several (array) cookies and returns it with a simple sanitization of the content.
	 *
	 * @deprecated The Cookie::get from FilterCookie::getString.
	 * @param string|array $cookie
	 * @return string|false
	 */
	static public function get( $cookies )
	{
		trigger_error( "'Cookie::get' is deprecated, please use 'FilterCookie'. Ex: FilterCookie::getInstance()->getString( 'cookie_key' );" );

		if ( is_array( $cookies ) )
		{
			foreach ( $cookies as $cookie )
			{
				$values[$cookie] = self::_sanitizeCookie( $cookie );
			}

			if ( !isset( $values ) )
			{
				return false;
			}

			return $values;
		}
		else
		{
			return self::_sanitizeCookie( $cookies );
		}
	}

	/**
	 * Returns a sanitized Cookie.
	 *
	 * @param array $cookies
	 * @return string|false
	 */
	static private function _sanitizeCookie( $cookie )
	{
		if ( FilterCookie::getInstance()->isSent( $cookie ) )
		{
			return FilterCookie::getInstance()->getString( $cookie );
		}

		return false;
	}

    static protected function setCookie(string $name, $value = "", $expires = 0, $path = "", $domain = "", $secure = false, $httponly = false, string $samesite = null): bool
    {
        $options = [
            'expires' => $expires,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly,
        ];

        if ( $samesite !== null) {
            $options['samesite'] = $samesite;
        }

        return setcookie( $name, $value, $options );
    }
}
