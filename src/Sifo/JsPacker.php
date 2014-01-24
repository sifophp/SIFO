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
	 * @param array $media_list List of media files included in the pack.
	 * @param string $prepend_string Prepended content.
	 */
	protected function getPackedContent( Array $media_list, $prepend_string = '' )
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
			else
			{
				$content .= "alert( 'File {$media['name']} not found' );";
			}
		}

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
var	sHostStatic = window.sHostStatic ? window.sHostStatic : "{$this->instance_static_host}",
	Hash = window.Hash ? window.Hash : "unset",
	sInstance = window.sInstance ? window.sInstance : '',
	basePathConfig = {\n\t
CODE;
		foreach ( $media_list as $group => $media_data )
		{
			$base_array[] = "\t'$group': sHostStatic + '/{$this->generated_files_public_path}/' + sInstance + '$group.js?rev=' + Hash";
		}

		$base_code .= implode( ",\n", $base_array ) . "\n\t};\n";

		return $base_code;

	}

}