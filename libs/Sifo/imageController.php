<?php
/**
 * LICENSE
 *
 * Copyright 2010 Sergi Ambel
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

abstract class ImageController extends Controller
{
	/**
	 * Used in the returned info header.
	 *
	 * @var string
	 */
	protected $content_type;

	/**
	 * Flag controlling if the controller should behave with a specific response.
	 */
	public $is_gif = false;

	/**
	 * Flag controlling if the controller should behave with a specific response.
	 */
	public $is_jpeg = false;

	/**
	 * Flag controlling if the controller should behave with a specific response.
	 */
	public $is_png = false;

	/**
	 * Flag controlling if the controller should behave with a specific response.
	 */
	public $is_bmp = false;

	/**
	 * Flag controlling if the controller should behave with a specific response.
	 */
	public $is_ico = false;

	/**
	 * Returns the content-type string identification when we want customize the header.
	 */
	protected function customizeHeader()
	{
		if ( !( $this->is_gif || $this->is_jpeg || $this->is_png || $this->is_bmp || $this->is_ico ) || ( $this->is_gif + $this->is_jpeg + $this->is_png + $this->is_bmp + $this->is_ico ) > 1 )
		{
			throw new Exception_500( "Please do the favour of use one (and only one) image content_type helper!" );
		}

		if ( $this->is_gif )
		{
			$content_type ='Content-type: image/gif';
		}

		if ( $this->is_jpeg )
		{
			$content_type ='Content-type: image/jpeg';
		}

		if ( $this->is_png )
		{
			$content_type ='Content-type: image/png';
		}

		if ( $this->is_bmp )
		{
			$content_type ='Content-type: image/bmp';
		}

		if ( $this->is_ico )
		{
			$content_type ='Content-type: image/x-icon';
		}

		header( "Content-type: $content_type" );
	}

	/**
	 * Returns tha contents in cache or false.
	 *
	 * @return mixed
	 */
	protected function grabCache()
	{
        if ( Domains::getInstance()->getDevMode() && ( FilterCookie::getInstance()->getInteger( 'rebuild_all' ) || FilterGet::getInstance()->getInteger( 'rebuild' ) ) )
		{
			return false;
		}

		$cache_key = $this->parseCache();
		// Controller does not uses cache:
		if ( !$cache_key )
		{
			return false;
		}

		if ( CacheDisk::singleton()->hasExpired( $cache_key['name'], $cache_key['expiration'] ) )
		{
			return false;
		}

		$content = CacheDisk::singleton()->get( $cache_key['name'] );

		return ( $content ? $content : false );
	}

	/**
	 * Dispatch the controller.
	 */
	public function dispatch()
	{
		$this->getClass( "CacheDisk" );

		$this->customizeHeader();

		$this->preDispatch();
		$cached_content = $this->grabCache();
		if ( false !== $cached_content )
		{
			echo $cached_content;
			$this->postDispatch();
			return;
		}
		$cache_key = $this->parseCache();

		$content = $this->build();

		if ( false !== $cache_key )
		{
			CacheDisk::singleton()->set( $cache_key['name'], $content, self::CACHE_COMPRESS, $cache_key['expiration'] );
		}

		$this->postDispatch();
		echo $content;
	}

}