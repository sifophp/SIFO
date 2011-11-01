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
 * Generates media files.
 */
abstract class MediaPacker
{

	/**
	 * Static path of the current instance.
	 *
	 * @var string
	 */
	protected $generated_files_folder;

	/**
	 * Media type, specified in the children.
	 *
	 * @var string
	 */
	protected $media_type;

	public function __construct()
	{
		$this->working_instance = Config::getInstance()->getInstanceName();
		$this->instance_static_host = Domains::getInstance()->getStaticHost();
		$this->generated_files_folder = ROOT_PATH . '/instances/' . $this->working_instance . '/public/static/' . $this->media_type . '/generated/';
	}

	/**
	 * Takes all the available combinations of media files and creates all the packs.
	 */
	public function packMedia()
	{
		$media = Config::getInstance()->getConfig( $this->media_type );

		foreach ( $media as $group => $media_name )
		{
			ksort( $media[$group] ); // Reorder elements by priority key, no matter how the array was created.
			$file = $this->generated_files_folder . $group . '.' . $this->media_type;

			// Add the basepath definition at the beginning of the 'default' JS file:
			$prepend_string = ( 'default' == $group && 'js' == $this->media_type ? $this->getBasePathConfig( $media ) : '');

			$this->writePackedContent( $file, $media[$group], $prepend_string );
		}
	}

}