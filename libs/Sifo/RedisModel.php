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

class RedisModel
{
	/**
	 * Redis client object.
	 * @var Predis_Client
	 */
	private static $connected_client = null;

	private static $connection_params;

	/**
	 * Connect to redis and return a redis object to start passing commands.
	 *
	 * @param string $db_params Connection settings array.
	 * @return Predis_Client
	 */
	public function connect( $db_params = null )
	{
		self::$connection_params = sha1( serialize( $db_params ) );

		if ( !isset( self::$connected_client[self::$connection_params] ) )
		{
			$version = Config::getInstance()->getLibrary( 'predis' );
			include_once ROOT_PATH . '/libs/'.$version .'/lib/Predis/Autoloader.php';
			\Predis\Autoloader::register();
			include_once ROOT_PATH . '/libs/'.$version . '/lib/Predis/Client.php';

			if ( empty( $db_params ) )
			{
				$db_params = Domains::getInstance()->getParam( 'redis' );
			}
			self::$connected_client[self::$connection_params] = new \Predis\Client( $db_params );
		}

		return self::$connected_client[self::$connection_params];
		//return Predis_Client::create( Config::getInstance()->getConfig( 'redis' ) );
	}

	/**
	 * Disconnect from server and reset the static object for reconnection.
	 */
	public function disconnect()
	{
		self::$connected_client[self::$connection_params]->disconnect();
		self::$connected_client[self::$connection_params] = null;
	}

	/**
	 * Disconnect clients on object destruction.
	 *
	 * TODO: Only disconnects last.
	 */
	public function __destruct()
	{
		if ( null !== self::$connected_client[self::$connection_params] )
		{
			$this->disconnect();
		}
	}
}