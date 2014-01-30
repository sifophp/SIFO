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
 * Simple class used to shadow and unshadow strings. NOT SECURE FOR SENSITIVE DATA.
 *
 * Its aim is to make strings non human readable, for instance, passing IDs via URL.
 * This class shouldn't be used to cypher passwords or any other sensitive data.
 *
 * The name 'Crypt' has been left for historic reasons, do not see it as a symetric password function.
 */
class Crypt
{
	/**
	 * Seed used for crypt/decrypt strings, in short this adds the mess.
	 *
	 * @var string
	 */
	static public $seed = 'WriteSomeTextHere';

	static public function encrypt( $string )
	{
		$result = '';
		for( $i=0; $i<strlen( $string ); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr( self::$seed, ($i % strlen( self::$seed ) )-1, 1 );
			$char = chr( ord( $char ) + ord( $keychar ) );
			$result .= $char;
		}

		return base64_encode( $result );
	}

	static public function decrypt( $string )
	{
		$result ='';
		$string = base64_decode( $string );

		for( $i=0; $i<strlen( $string ); $i++ ) {
			$char = substr( $string, $i, 1);
			$keychar = substr( self::$seed, ( $i % strlen( self::$seed ) )-1, 1 );
			$char = chr( ord( $char ) - ord( $keychar ) );
			$result .= $char;
		}

		return $result;
	}

	static public function encryptForUrl( $string, $char_plus = '-', $char_slash = '.' )
	{
		$string = self::encrypt( $string );
		$string = str_replace( '+', $char_plus, $string );
		$string = str_replace( '/', $char_slash, $string );
		return $string;
	}

	static public function decryptFromUrl( $string, $char_plus = '-', $char_slash = '.' )
	{
		$string = str_replace( $char_plus, '+', $string );
		$string = str_replace( $char_slash, '/', $string );
		return self::decrypt( $string );
	}
}