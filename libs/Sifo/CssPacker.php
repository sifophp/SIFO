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
 * CSS packer.
 */
class CssPacker extends \Sifo\MediaPacker
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
