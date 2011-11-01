<?php
namespace Common;

namespace Common;
/**
 * User functions.
 *
 * @author Jorge Tarrero (thedae@gmail.com)
 */
class HelperPasswordGenerator
{
	const EVEN_CHARS = 'aeiou';

	const ODD_CHARS = 'bcdfghjklmnpqrstvwyz';

	const ALL_LOWER_CASE = 0;

	const VOWEL_UPPER_CASE = 1;

	const MIXED_CASE = 2;

	/**
	 * Generate a random human readable password.
	 *
	 * @param integer $strength Strength of the password.
	 * @param integer $lenght Lenght of the password (excluding the final numbers).
	 * @param integer $append_numbers Lenght of the number to append.
	 * @return unknown
	 */
	public static function beNice( $strength = self::ALL_LOWER_CASE, $lenght = 8, $append_numbers = 2 )
	{
		$password = '';

		$vocals = self::EVEN_CHARS;
		$consonants = self::ODD_CHARS;

		// Strength 1: Add upper case to vocals.
		if ( $strength > 0 )
		{
			$vocals .= strtoupper( $vocals );
		}

		// Strength 2: Add upper case to consonants.
		if ( $strength > 1 )
		{
			$consonants .= strtoupper( $consonants );
		}

		for ( $i = 0; $i < $lenght; $i++ )
		{
			if ( ( $i % 2 ) > 0 )
			{
				$password .= $vocals[( rand() % strlen( $vocals ) )];
			}
			else
			{
				$password .= $consonants[( rand() % strlen( $consonants ) )];
			}
		}

		// We want the first character to be upper.
		$password[0] = strtoupper( $password[0] );

		// If specified, we can add a number at the final of the password.
		if ( $append_numbers > 0 )
		{
			$number = 0;
			while ( strlen( $number ) < $append_numbers )
			{
				$number = ( rand() % ( pow( 10, $append_numbers ) - 1 ) );
			}

			$password .= $number;
		}

		return $password;
	}
}

?>