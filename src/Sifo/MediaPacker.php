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
 *	 http://www.apache.org/licenses/LICENSE-2.0
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
	 * Relative and public path to the generated content (as served by the static server).
	 *
	 * E.g: js/generated
	 *
	 * @var string
	 */
	public $generated_files_public_path;

	/**
	 * Filesystem path where the files will be packed and saved to.
	 *
	 * E.g: /var/www/sifo/instances/myinstance/public/static/js/generated
	 *
	 * @var string
	 */
	public $generated_files_folder;

	/**
	 * Media type, specified in the children.
	 *
	 * @var string
	 */
	protected $media_type;

	/**
	 * Write media pack in disk.
	 *
	 * @param string $pack_filename File name of the generated media pack.
	 * @param array $media_list List of media files included in the pack.
	 * @param string $prepend_string Prepended content.
	 */
	abstract protected function getPackedContent( Array $media_list, $prepend_string = '' );

	public function __construct()
	{
		$this->working_instance = Config::getInstance()->getInstanceName();
		$this->instance_static_host = Domains::getInstance()->getStaticHost();

		// Contents will be accessible by static server under this path:
		$this->generated_files_public_path = $this->media_type . '/generated';

		// Packed contents will be stored under this filesystem path. You can overwrite this property:
		$this->generated_files_folder = ROOT_PATH . '/instances/' . $this->working_instance . '/public/static/' . $this->generated_files_public_path;


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
			$file = $this->generated_files_folder . '/' . $group . '.' . $this->media_type;

			// Add the basepath definition at the beginning of the 'default' JS file:
			$prepend_string = ( 'default' == $group && 'js' == $this->media_type ? $this->getBasePathConfig( $media ) : '' );

			$content = $this->getPackedContent( $media[$group], $prepend_string );

			// Create subdirs if needed:
			$file_info = pathinfo( $file );
			if ( !is_dir( $file_info['dirname'] ) )
			{
				// Create directory recursively if does not exist yet.
				mkdir( $file_info['dirname'], 0755, true );
			}

			// Write packed file to disk:
			file_put_contents( $file, $content );
		}
	}

	/**
	 * Sets the directory where you want to write the generated files to.
	 *
	 * @param $path Real path to the directory storing
	 *
	 * @throws \RuntimeException
	 */
	public function setGeneratedFolder( $path )
	{
		if ( is_dir( $path ) )
		{
			$this->generated_files_folder = $path;
		}
		else
		{
			throw new \RuntimeException( 'Path given to store generated content is not a valid dir.' );
		}

	}

}