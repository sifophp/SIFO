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

/**
 * Filters the request array checking that the values accomplish the given filters.
 *
 * It DOES NOT modify the original value. Use SANITIZE filters for that purpose.
 *
 * @see http://php.net/manual/en/filter.filters.validate.php
 */
class Filter
{
	/**
	 * Regular expression for email validation.
	 */
	const VALID_EMAIL_REGEXP = '/^[A-Z0-9._%\-+]+@(?:[A-Z0-9\-]+\.)+(?:[A-Z]{2}|com|edu|org|net|biz|info|name|aero|biz|info|jobs|travel|museum|name|cat|asia|coop|jobs|mobi|tel|pro|arpa|gov|mil)$/i';

	/**
	 * Singleton object.
	 *
	 * @var Filter
	 */
	static protected $instance;

	/**
	 * Request storage.
	 * @var array
	 */
	protected $request;

	protected function __construct( $request )
	{
		$this->request = &$request;
	}

	/**
	 * Singleton for filtering. Uses POST by default.
	 *
	 * @return Filter
	 */
	public static function getInstance()
	{
		if ( !self::$instance )
		{
			self::$instance = new self ( $_POST );
			$_POST = array();
		}
		return self::$instance;
	}

	/**
	 * Checks if a var has been sent in the request.
	 *
	 * @param string $var_name
	 */
	public function isSent( $var_name )
	{
		return isset( $this->request[$var_name] );
	}

	/**
	 * Returns the names of the sent Vars.
	 */
	public function sentVars()
	{
		return array_keys( $this->request );
	}

	public function isEmpty( $var_name )
	{
		// I changed empty by strlen because we was sending that 0 is an empty field and this is a correct integer. Minutes for example:
		return ( !isset( $this->request[$var_name] ) || ( is_array($this->request[$var_name]) && ( count( $this->request[$var_name] ) == 0 ) ) || ( !is_array($this->request[$var_name]) && (  strlen( $this->request[$var_name] ) == 0 ) ) );
	}

	/**
	 * Returns the number of variables found in the post.
	 *
	 * @return integer
	 */
	public function countVars()
	{
		return count( $this->request );
	}


	/**
	 * Returns a string using the FILTER_DEFAULT.
	 *
	 * @param string $var_name
	 * @return string
	 */
	public function getString( $var_name, $sanitized = false )
	{
		if ( !isset( $this->request[$var_name] ) )
		{
			return false;
		}

		if ( false === $sanitized)
		{
			return filter_var( $this->request[$var_name], FILTER_DEFAULT );
		}
		else
		{
			// Used the flag encode LOW because allows Chinese Characters (encode HIGH don't): 地 图
			return filter_var( $this->request[$var_name], FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW );
		}
	}

	/**
	 * Get a variable without any type of filtering.
	 *
	 * @param string $var_name
	 * @return string
	 */
	public function getUnfiltered( $var_name )
	{
		if ( !isset( $this->request[$var_name] ) )
		{
			return false;
		}

		return $this->request[$var_name];
	}

	/**
	 * Returns an email if filtered or false if it is not valid.
	 *
	 * @param string $var_name Request containing the variable.
	 * @param boolean $check_dns Check if domain passed has a valid MX record.
	 * @return string
	 */
	public function getEmail( $var_name, $check_dns = false )
	{
		if ( !isset( $this->request[$var_name] ) )
		{
			return false;
		}

		if ( preg_match( self::VALID_EMAIL_REGEXP, $this->request[$var_name] ) )
		{
			if ( $check_dns )
			{
				list( $username, $domain ) = split( '@', $this->request[$var_name] );
				return ( checkdnsrr( $domain, 'MX' ) ? $this->request[$var_name] : false );
			}
			else
			{
				return $this->request[$var_name];
			}
		}

		return false;
		// Vulnerable: return filter_var( $this->request[$var_name], FILTER_VALIDATE_EMAIL );
	}

	/**
	 * Returns if a value might be considered as boolean (1, true, on, yes)
	 *
	 * @param string $var_name
	 * @return boolean
	 */
	public function getBoolean( $var_name )
	{
		if ( !isset( $this->request[$var_name] ) )
		{
			return false;
		}

		return filter_var( $this->request[$var_name], FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Returns a float value for the given var.
	 *
	 * @param string $var_name
	 * @param boolean $decimal
	 * @return float
	 */
	public function getFloat( $var_name, $decimal = null )
	{
		if ( !isset( $this->request[$var_name] ) )
		{
			return false;
		}

		if ( isset( $decimal ) )
		{
			$decimal = array( 'options' => array( 'decimal' => $decimal ) );
		}

		return filter_var( $this->request[$var_name], FILTER_VALIDATE_FLOAT, $decimal );
	}

	/**
	 * Returns the integer value of the var or false.
	 *
	 * @param string $var_name
	 * @param boolean $decimal
	 * @return integer
	 */
	public function getInteger( $var_name, $min_range = null, $max_range = null )
	{
		if ( !isset( $this->request[$var_name] ) )
		{
			return false;
		}

		$options = null;

		if ( isset( $min_range ) )
		{
			$options['options']['min_range'] = $min_range;
		}

		if ( isset( $max_range ) )
		{
			$options['options']['max_range'] = $max_range;
		}

		return filter_var( $this->request[$var_name], FILTER_VALIDATE_INT, $options );
	}

	/**
	 * Returns the IP value of the var or false.
	 *
	 * @param string $var_name
	 * @param boolean $decimal
	 * @return integer
	 */
	public function getIP( $var_name, $min_range = null, $max_range = null )
	{
		if ( !isset( $this->request[$var_name] ) )
		{
			return false;
		}

		// Allow IPv4 Ips.
		$options['flags'] = FILTER_FLAG_IPV4;

		if ( isset( $min_range ) )
		{
			$options['options']['min_range'] = $min_range;
		}

		if ( isset( $max_range ) )
		{
			$options['options']['max_range'] = $max_range;
		}

		return filter_var( $this->request[$var_name], FILTER_VALIDATE_IP, $options );
	}

	public function getRegexp( $var_name, $regexp )
	{
		if ( !isset( $this->request[$var_name] ) )
		{
			return false;
		}

		return filter_var( $this->request[$var_name], FILTER_VALIDATE_REGEXP, array( 'options' => array( 'regexp' => $regexp ) ) );
	}

	public function getUrl( $var_name )
	{
		if ( !isset( $this->request[$var_name] ) )
		{
			return false;
		}

		$options['options']['flags'] = FILTER_FLAG_PATH_REQUIRED;
		return filter_var( $this->request[$var_name], FILTER_VALIDATE_URL, $options );
	}

	public function getInArray( $var_name, Array $list_of_elements )
	{
		if ( !isset( $this->request[$var_name] ) )
		{
			return false;
		}

		if ( in_array( $this->request[$var_name], $list_of_elements, true ) )
		{
			return $this->request[$var_name];
		}

		return false;
	}

	/**
	 * Return an array like getArray but, in this case, the array is a serialized one.
	 * Used to send arrays from javascript.
	 *
	 * @param string $var_name
	 * @param string $filter_function Is the function to use with each array field.
	 * @return boolean 
	 */
	public function getArrayFromSerialized( $var_name, $filter_function = null )
	{
		if ( !isset( $this->request[$var_name] ) )
		{
			return false;
		}
		parse_str( $this->request[$var_name], $this->request[$var_name] );
		return $this->getArray( $var_name, $filter_function );
	}

	/**
	 * Returns an array on the post UNFILTERED.
	 *
	 * @param unknown_type $var_name
	 * @return unknown
	 */
	public function getArray( $var_name, $filter_function = null )
	{

		if ( !isset( $this->request[$var_name] ) || !is_array( $this->request[$var_name] ) )
		{
			return false;
		}

		// Returns an unfiltered Array
		if ( null == $filter_function )
		{
			return $this->request[$var_name];
		}

		trigger_error( 'The function Filter::getArray is not implemented yet so you are not filtering anything.' );

		/*
		TODO: Filter arrays, but filter must be independent of the request.
		foreach ( $this->request[$var_name] as $key => $value )
		{
			$params = func_get_args();
			unset( $params[0] );

			// Prepend the value to the beginning of the array:
			array_unshift( $params, $value );

			$filtered_array[$key] = call_user_func_array( array( $this, $filter_function ), $params );
		}

		return $filtered_array;
		*/

	}

	/**
	 * Checks if a string is a valid.
	 *
	 * Matches:
	 * 1/1/2005 | 29/02/12 | 29/02/2400
	 * Non-Matches:
	 * 29/2/2005 | 29/02/13 | 29/02/2200
	 *
	 * Until PHP 5.3 is not widely used the DateTime won't be used.
	 *
	 * @param string $var_name
	 * @param string $format (UNUSED yet) Any format accepted by date()
	 * @return mixed String of the date or false.
	 */
	public function getDate( $var_name, $format = 'd-m-Y' )
	{
		if ( !isset( $this->request[$var_name] ) )
		{
			return false;
		}

		// Matching a Date in mm/dd/yyyy Format
		if ( preg_match( '/^(((0?[1-9]|[12]\d|3[01])[\.\-\/](0?[13578]|1[02])[\.\-\/]((1[6-9]|[2-9]\d)?\d{2}|\d))|((0?[1-9]|[12]\d|30)[\.\-\/](0?[13456789]|1[012])[\.\-\/]((1[6-9]|[2-9]\d)?\d{2}|\d))|((0?[1-9]|1\d|2[0-8])[\.\-\/]0?2[\.\-\/]((1[6-9]|[2-9]\d)?\d{2}|\d))|(29[\.\-\/]0?2[\.\-\/]((1[6-9]|[2-9]\d)?(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)|00|[048])))$/i', $this->request[$var_name] ) )
		{
			return $this->request[$var_name];
		}
		// PHP 5.3 function: DateTime::createFromFormat( $format, $this->request[$var_name] )
		
		return false;
	}

	/**
	 * Get the raw request array as it was received, unfiltered.
	 *
	 * @return array
	 */
	public function getRawRequest()
	{
		return $this->request;
	}
}

/**
 * Filter is FilterPost by default.
 */
class FilterPost extends Filter { }

class FilterGet extends Filter
{
	/**
	 * Singleton object.
	 *
	 * @var Filter
	 */
	static protected $instance;

	/**
	 * Filters variables passed by Get
	 * @return Filter
	 */
	public static function getInstance()
	{
		if ( !self::$instance )
		{
			self::$instance = new self ( $_GET );
			$_GET = array();
		}
		return self::$instance;
	}
}

class FilterRequest extends Filter
{
	/**
	 * Singleton object.
	 *
	 * @var Filter
	 */
	static protected $instance;

	/**
	 * Filters variables passed by Post, Get and Cookie.
	 * @return Filter
	 */
	public static function getInstance()
	{
		if ( !self::$instance )
		{
			self::$instance = new self ( $_REQUEST );
			$_REQUEST = array();
		}
		return self::$instance;
	}
}

class FilterServer extends Filter
{
	/**
	 * Singleton object.
	 *
	 * @var Filter
	 */
	static protected $instance;

	/**
	 * Filters variables passed by Server (Apache SetEnv for instance)
	 * @return Filter
	 */
	public static function getInstance()
	{
		if ( !self::$instance )
		{
			self::$instance = new self ( $_SERVER );
			//$_SERVER = array();		//Too soon to remove the $_SERVER variable. It's being used in lots of places yet.
			// ¡Lombarte! ¡Lombarte!, ¡Lombarte es cojonudo!, ¡como Lombarte no hay ninguno!
		}
		return self::$instance;
	}

	/**
	 * Mocks the host for use in scripts.
	 * @param string $mocked_host
	 */
	public function setHost( $mocked_host )
	{
		$this->request['HTTP_HOST'] = $mocked_host;
	}

}

class FilterCookie extends Filter
{
	/**
	 * Singleton object.
	 *
	 * @var Filter
	 */
	static protected $instance;

	/**
	 * Filters variables passed inside Cookies.
	 * @return Filter
	 */
	public static function getInstance()
	{
		if ( !self::$instance )
		{
			self::$instance = new self ( $_COOKIE );
		}
		return self::$instance;
	}
}

class FilterSession extends Filter
{
	/**
	 * Singleton object.
	 *
	 * @var Filter
	 */
	static protected $instance;

	/**
	 * Filters variables passed by Session.
	 * @return Filter
	 */
	public static function getInstance()
	{
		if ( !self::$instance )
		{
			self::$instance = new self ( $_SESSION );
		}
		return self::$instance;
	}
}

class FilterFiles extends Filter
{
	/**
	 * Singleton object.
	 *
	 * @var Filter
	 */
	static protected $instance;

	/**
	 * Filters variables passed by File uploads.
	 * @return Filter
	 */
	public static function getInstance()
	{
		if ( !self::$instance )
		{
			self::$instance = new self ( $_FILES );
			$_FILES = array();
		}
		return self::$instance;
	}

	/**
	 * Get a variable without any type of filtering.
	 *
	 * @param string $var_name
	 * @return string
	 */
	public function getUnfiltered( $var_name )
	{
		$file = parent::getUnfiltered( $var_name );

		if ( UPLOAD_ERR_NO_FILE == $file['error'] )
		{
			return false;
		}

		return $file;
	}

}

class FilterEnv extends Filter
{
	/**
	 * Singleton object.
	 *
	 * @var Filter
	 */
	static protected $instance;

	/**
	 * Filters variables passed in the environment.
	 * @return Filter
	 */
	public static function getInstance()
	{
		if ( !self::$instance )
		{
			self::$instance = new self ( $_ENV );
			$_ENV = array();
		}
		return self::$instance;
	}
}

class FilterCustom extends Filter
{
	/**
	 * Singleton object.
	 *
	 * @var Filter
	 */
	static protected $instance;

	/**
	 * Filters variables passed in the array and empties original input.
	 *
	 * @param array $array Array with filtration purposes.
	 * @return Filter
	 */
	public static function getInstance()
	{
		$params = func_get_args();
		if ( ( !isset( $params[0] ) ) || ( !is_array( $params[0] ) ) )
		{
			throw new FilterException( 'The variable passed inside the getInstance( $array ) method is not an array.' );
		}
		$array = $params[0];
		if ( !self::$instance )
		{
			self::$instance = new self ( $array );
		}
		return self::$instance;
	}
}

class FilterException extends Exception {}