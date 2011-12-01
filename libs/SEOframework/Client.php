<?php

/**
 * LICENSE
 *
 * Copyright 2010 Albert Garcia
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
 * Class for extracting info from application client: IP, origin country, region & city, browser (version, capabilities, preferences...), SO.
 */
class Client
{

	static private $instance;

	/**
	 * Filter server.
	 * @var FilterServer
	 */
	static private $server;

	/**
	 * Singleton of Client class.
	 *
	 * @param string $instance_name Instance Name, needed to determine correct paths.
	 * @return object Client
	 */
	public static function getInstance()
	{
		if ( !isset( self::$instance ) )
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct()
	{

	}

	/**
	 * Get 2 letter country code from client IP.
	 */
	public static function getCountryCode()
	{

		if ( Registry::KeyExists( 'Client_CountryCode' ) )
		{
			$country_code = Registry::get( 'Client_CountryCode' );
		}
		else
		{
			require_once ROOT_PATH . '/libs/GeoIP-Lite/geoip.php';
			$gi = geoip_open( ROOT_PATH . '/libs/GeoIP-Lite/GeoIP.dat', GEOIP_MEMORY_CACHE );
			$country_code = geoip_country_code_by_addr( $gi, self::getIP() );
			geoip_close( $gi );
			Registry::set( 'Client_CountryCode', $country_code );
		}

		return $country_code;
	}

	/**
	 * Get country name from client IP.
	 */
	public static function getCountryName()
	{

		if ( Registry::KeyExists( 'Client_CountryName' ) )
		{
			$country_name = Registry::get( 'Client_CountryName' );
		}
		else
		{
			require_once ROOT_PATH . '/libs/GeoIP-Lite/geoip.php';
			$gi = geoip_open( ROOT_PATH . '/libs/GeoIP-Lite/GeoIP.dat', GEOIP_MEMORY_CACHE );
			$country_name = geoip_country_name_by_addr( $gi, self::getIP() );
			geoip_close( $gi );
			Registry::set( 'Client_CountryName', $country_name );
		}

		return $country_name;
	}

	/**
	 * Get browser information.
	 */
	public static function getBrowser( $useragent = null, $return_array = false )
	{
		if ( Registry::keyExists( 'Client_Browser' ) )
		{
			$browser = Registry::get( 'Client_Browser' );
		}
		else
		{
			require_once ROOT_PATH . '/libs/Browscap/Browscap.php';
			$bc = new Browscap( ROOT_PATH . '/libs/Browscap/' );
			$browser = $bc->getBrowser( $useragent, $return_array );
			Registry::set( 'Client_Browser', $browser );
		}

		return $browser;
	}

	/**
	 * Get browser default language.
	 */
	public static function getBrowserLanguage()
	{
		$server = FilterServer::getInstance();

		if ( $lang = $server->getString( 'HTTP_ACCEPT_LANGUAGE' ) )
			return self::parseDefaultLanguage( $lang );
		else
			return self::parseDefaultLanguage( NULL );
	}

	private static function parseDefaultLanguage( $http_accept, $deflang = "es-es" )
	{
		if ( isset( $http_accept ) && strlen( $http_accept ) > 1 )
		{
			# Split possible languages into array
			$x = explode( ",", $http_accept );
			foreach ( $x as $val )
			{
				#check for q-value and create associative array. No q-value means 1 by rule
				if ( preg_match( "/(.*);q=([0-1]{0,1}\.\d{0,4})/i", $val, $matches ) )
					$lang[$matches[1]] = ( float ) $matches[2];
				else
					$lang[$val] = 1.0;
			}

			#return default language (highest q-value)
			$qval = 0.0;
			foreach ( $lang as $key => $value )
			{
				if ( $value > $qval )
				{
					$qval = ( float ) $value;
					$deflang = $key;
				}
			}
		}
		return strtolower( $deflang );
	}

	/**
	 * Get real client IP.
	 */
	public static function getIP()
	{
		$client_ip = "";

		$server = FilterServer::getInstance();

		if ( $server->getIp( "HTTP_X_FORWARDED_FOR" ) )
		{
			$ip = $server->getIp( "HTTP_X_FORWARDED_FOR" );
		}
		elseif ( $server->getIp( "HTTP_CLIENT_IP" ) )
		{
			$ip = $server->getIp( "HTTP_CLIENT_IP" );
		}
		else
		{
			$ip = $server->getIp( "REMOTE_ADDR" );
		}

		// Simulated IP, only when a local IP is detected, to allow geolocation services work.
		if ( strstr( $ip, '127.0.0.' ) || strstr( $ip, '192.168.' ) )
		{
			$ip = '89.140.161.225';
		}

		// From http://www.eslomas.com/index.php/archives/2005/04/26/obtencion-ip-real-php/
		$entries = preg_split( '/[,\s]/', $ip );

		reset( $entries );

		while ( list (, $entry ) = each( $entries ) )
		{
			$entry = trim( $entry );

			if ( preg_match( "/^([0-9]+.[0-9]+.[0-9]+.[0-9]+)/", $entry, $ip_list ) )
			{
				// http://www.faqs.org/rfcs/rfc1918.html
				$private_ip = array(
					'/^0./',
					'/^127.0.0.1/',
					'/^192.168..*/',
					'/^172.((1[6-9])|(2[0-9])|(3[0-1]))..*/',
					'/^10..*/' );

				$found_ip = preg_replace( $private_ip, $client_ip, $ip_list[1] );

				if ( $client_ip != $found_ip )
				{
					$ip = $found_ip;
					break;
				}
			}
		}

		return trim( $ip );
	}

	/**
	 * Determines if actual client is a Crawler
	 * based on USERAGENTS contained in Browscap library.
	 */
	public static function isCrawler()
	{
		if ( Registry::keyExists( 'Client_isCrawler' ) )
		{
			return Registry::get( 'Client_isCrawler' );
		}
		else
		{
			$browser_info = self::getBrowser();
			$answer = true;
			if ( false == $browser_info->Crawler || empty( $browser_info->Crawler ) )
			{
				$answer = false;
			}

			Registry::set( 'Client_isCrawler', $answer );

			return $answer;
		}
	}
}