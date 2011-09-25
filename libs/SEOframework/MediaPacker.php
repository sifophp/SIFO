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

/**
 * CSS packer.
 */
class CssPacker extends MediaPacker
{
	/**
	 * Media type of the current packer.
	 *
	 * @var string
	 */
	protected $media_type = 'css';

	/**
	 * Write media pack in disk.
	 *
	 * @param string $pack_filename File name of the generated media pack.
	 * @param array $media_list List of media files included in the pack.
	 * @param string $prepend_string Prepended content.
	 */
	protected function writePackedContent( $pack_filename, Array $media_list, $prepend_string = '' )
	{
		$content = $prepend_string;
		foreach ( $media_list['files'] as $media )
		{
			$filename = ROOT_PATH . '/' . $media;
			$path_info = pathinfo( $filename );

			if ( is_file( $filename ) )
			{
				$content .= "/* START FILE {$path_info['basename']} */\n\n" . chr( 13 );
				$content .= file_get_contents( $filename ) . chr( 13 );
				$content .= "\n\n/* END {$path_info['basename']} */" . chr( 13 );
			}
		}

		file_put_contents( $pack_filename, $content );

		return $content;

	}
}

/**
 * Javascript packer.
 */
class JsPacker extends MediaPacker
{
	/**
	 * Media type of the current packer.
	 *
	 * @var string
	 */
	protected $media_type = 'js';

		/**
	 * Write media pack in disk.
	 *
	 * @param string $pack_filename File name of the generated media pack.
	 * @param array $media_list List of media files included in the pack.
	 * @param string $prepend_string Prepended content.
	 */
	protected function writePackedContent( $pack_filename, Array $media_list, $prepend_string = '' )
	{
		$content = $prepend_string;
		foreach ( $media_list as $media )
		{
			$filename = ROOT_PATH . '/' . $media['filename'];

			if ( is_file( $filename ) )
			{
				$content .= "/* BEGIN {$media['name']} */\n\n" . chr( 13 );
				$content .= file_get_contents( $filename ) . chr( 13 );
				$content .= "\n\n/* END {$media['name']} */" . chr( 13 );
			}
		}

		file_put_contents( $pack_filename, $content );

		return $content;

	}

	/**
	 * The Basepath is a Javascript array containing the absolute location of every group.
	 *
	 * @param array $media_list The list of media files that has been generated.
	 * @param array $generated_files The resultant generated files.
	 * @return string
	 */
	protected function getBasePathConfig( Array $media_list )
	{
		$base_code = <<<CODE
sHostStatic = window.sHostStatic ? window.sHostStatic : "{$this->instance_static_host}";
Hash = window.Hash ? window.Hash : "unset";
var basePathConfig = {\n
CODE;
		foreach ( $media_list as $group => $media_data )
		{
			$base_array[] = "\t'$group': sHostStatic + '/js/generated/$group.js?rev=' + Hash";
		}

		$base_code .= implode( ",\n", $base_array ) . "\n};\n";

		return $base_code;

	}

}