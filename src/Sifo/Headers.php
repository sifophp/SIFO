<?php
/**
 * LICENSE
 *
 * Copyright 2012 Albert Lombarte
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
 * Stack of HTTP headers to be sent to the browser just before the output.
 *
 * Use "set" method to add headers to the stack.
 * Use "setResponseStatus" for HTTP codes only.
 * Use "send" to send all the headers to the browser.
 *
 * Examples:
 *
 * // Set several headers with the same key ('replace' parameter at the end set to false)
 * Headers::set( 'WWW-Authenticate', 'Negotiate' )
 * Headers::set( 'WWW-Authenticate', 'NTLM', false )
 *
 * // Will send:
 * WWW-Authenticate: Negotiate
 * WWW-Authenticate: NTLM
 *
 * Headers::set( 'Content-Type', 'application/pdf' )
 * Headers::set( 'Content-Type', 'application/json' )
 *
 * // Will send:
 * Content-Type: application/json
 * (pdf is ignored because the "replace" is true by default)
 *
 * The headers won't be sent until you execute:
 * Headers::send();
 */

class Headers
{
	/**
	 * Headers that are status codes. E.g: "HTTP/1.0 404 Not Found"
	 */
	public const FORMAT_TYPE_STATUS = 'HTTP/1.0 %s %s';

	/**
	 * Headers made of a key and a value. E.g: "WWW-Authenticate: Negotiate"
	 */
	public const FORMAT_KEY_VALUE = '%s: %s';

	/**
	 * List of all the headers sent by the application so far.
	 *
	 * @var array
	 */
	protected static $headers = array();

	/**
	 * Headers history.
	 *
	 * @var array
	 */
	protected static $history = array();



	/**
	 * Known HTTP codes by this framework.
	 */
	public static $http_codes = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => '( Unused )',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported'
	);

	public static function setDefaultHeaders()
	{
		self::set( 'Content-Type', 'text/html; charset=UTF-8' );
	}

	/**
	 * Creates a new header with the key and values passed.
	 *
	 * @param string $key The header name (e.g: Content-Type)
	 * @param string $value The value for the header (e.g: application/json)
	 * @param bool $replace Adds an additional value to any existing key.
	 */
	public static function set( $key, $value, $replace = true, $http_code = false )
	{
		self::pushHeader( $key, $value, $replace, self::FORMAT_KEY_VALUE, $http_code );
	}

	/**
	 * Creates the status header with the HTTP code that will be sent to the client.
	 *
	 * @param integer $http_code Http status code (e.g: 404)
	 */
	public static function setResponseStatus( $http_code )
	{
		if ( isset( self::$http_codes[$http_code] ) )
		{
			$msg = self::$http_codes[$http_code];
			self::pushHeader( ( string ) $http_code, $msg, true, self::FORMAT_TYPE_STATUS, false );
		}
		else
		{
			throw new Headers_Exception( "Unknown status code requested $http_code" );
		}

	}

	/**
	 * It formats the header and adds it to the stack.
	 *
	 * @param string $key Header name
	 * @param string $value Header value
	 * @param boolean $replace If the header overwrites any similar existing header.
	 * @param string $format The sprintf format used to format the content.
	 * @param integer $http_code Additional set of HTTP status code with the header. Suitable for "Location" header.
	 */
	protected static function pushHeader( $key, $value, $replace, $format, $http_code )
	{
		$header = array(
			'content' => sprintf( $format, $key, $value ),
			'replace' => $replace,
			'http_code' => $http_code
		);

		array_push( self::$headers, $header );
	}

	public static function get( $key )
	{
		if ( !isset( self::$headers[$key] ) )
		{
			return false;
		}

		return self::$headers[$key] ?: false;
	}

	public static function getAll()
	{
		return self::$headers;
	}

	/**
	 * Sends all the headers to the browser.
	 */
	public static function send()
	{
		foreach( self::$headers as $header => $values )
		{
			if ( $values['http_code'] )
			{
				header( $values['content'], $values['replace'], $values['http_code'] );
			}
			else
			{
				header( $values['content'], $values['replace'] );
			}
		}

		// Clear the stack after writing:
		self::$history[] = self::$headers;
		self::$headers = array();
	}

	/**
	 * Returns all the blocks of headers written so far.
	 *
	 * @return array
	 */
	public static function getDebugInfo()
	{
		return self::$history;
	}
}

class Headers_Exception extends \Exception {}