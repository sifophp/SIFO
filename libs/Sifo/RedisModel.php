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

include_once ROOT_PATH . '/libs/' . Config::getInstance()->getLibrary( 'predis' ) . '/lib/Predis/Autoloader.php';

/**
 * Customized Predis autoloader.
 */
class PredisAutoloader extends \Predis\Autoloader
{
    /**
     * Registers the autoloader class with the PHP SPL autoloader.
     *
     * Extended so the predis autoloader is PREPENDED instead of appended
     * to the autoload stack.
     */
    public static function register()
    {
        spl_autoload_register( array( new self, 'autoload' ), true, true );
    }
}

/**
 * Predis adapter for Sifo.
 */
class RedisModel
{
	/**
	 * Redis client object.
	 * @var Predis_Client
	 */
	private static $connected_client = array();

	private $profile;

	/**
	 * Connect to redis and return a redis object to start passing commands.
	 *
	 * If no profile is passed, default connection stated in domains.config.php is taken. Otherwise, profile
	 * will be searched in redis.config.php.
	 *
	 * @param string $profile Connection profile.
	 * @return Predis_Client
	 */
	public function connect( $profile = null )
	{
		if ( !isset( self::$connected_client[$profile] ) )
		{
			PredisAutoloader::register();

			// Connection taken from domains.config.php:
			if ( null == $profile )
			{
				$db_params = Domains::getInstance()->getParam( 'redis' );
			}
			else // Advanced configuration taken from redis.config.php
			{
				$db_params = Config::getInstance()->getConfig( 'redis', $profile );
			}

			self::$connected_client[$profile] = new \Predis\Client( $db_params );
			$this->profile = $profile;
		}

		return self::$connected_client[$profile];
	}

	/**
	 * Disconnect from server and reset the static object for reconnection.
	 */
	public function disconnect()
	{
		self::$connected_client[$this->profile]->disconnect();
		self::$connected_client[$this->profile] = null;
	}

	/**
	 * Disconnect clients on object destruction.
	 */
	public function __destruct()
	{
		foreach ( self::$connected_client as $client )
		{
			$client->disconnect();
		}
	}
}