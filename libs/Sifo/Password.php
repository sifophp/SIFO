<?php
/**
 * Based on code found at http://alias.io/2010/01/store-passwords-safely-with-php-and-mysql/
 */

namespace Sifo;

/**
 * Password hash generator and verifier.
 */
class Password
{

	const HASH_ITERATIONS = 5;

	const RANDOM_NOISE = 'kjf89olsd8[sad9';


	/**
	 * Returns a 128 character long hash for the given username and password. To be stored in your database.
	 *
	 * The "noise" is just noise, but stick with the same value once you start using a string.
	 *
	 * @param $username
	 * @param $password
	 * @param string $noise
	 * @return string 128 character long hash
	 */
	public function getHash( $username, $password, $noise = self::RANDOM_NOISE )
	{
		// Create a salt based on the username and some additional trash:
		$salt = hash( 'sha256', uniqid( mt_rand(), true ) . $noise . strtolower( $username ) );

		$hash = $salt . $password;

		// Hash and re-hash, and re-hash:
		for ( $i = 0; $i < self::HASH_ITERATIONS; $i++ )
		{
			$hash = hash( 'sha256', $hash );
		}

		// Prefix the hash with the salt so we can find it back later
		return $hash = $salt . $hash;
	}

	/**
	 * Given a password and hash determines if the combination is valid.
	 *
	 * @param $password
	 * @param $user_hash
	 * @return boolean
	 */
	public function isValid( $password, $user_hash )
	{

		// The first 64 characters of the hash is the salt
		$salt = substr( $user_hash, 0, 64 );

		$hash = $salt . $password;

		// Hash the password as we did before
		for ( $i = 0; $i < self::HASH_ITERATIONS; $i++ )
		{
			$hash = hash( 'sha256', $hash );
		}

		$hash = $salt . $hash;

		if ( $hash == $user_hash )
		{
			return true;
		}
		return false;
	}
}