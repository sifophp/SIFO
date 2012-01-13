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

namespace Sifo;

class Images
{
	/**
	 * Resize an image.
	 *
	 * @param file $from
	 * @param file $to
	 * @param integer $width
	 * @param integer $height
	 * @param false|array $crop
	 * @param boolean $resizeUp
	 * @param boolean $transparency
	 * @param int $quality
	 * @return boolean
	 */
	static public function resizeAndSave( $from, $to, $width, $height, $crop = false, $resizeUp = false, $transparency = false, $quality = 100 )
	{
		include_once ROOT_PATH . '/libs/' . Config::getInstance()->getLibrary( 'phpthumb' ) . '/ThumbLib.inc.php';

		$fileinfo = pathinfo( $to );

		$thumb = \PhpThumbFactory::create( $from );
		$thumb = \PhpThumbFactory::create( $from, array(
					'resizeUp' => $resizeUp,
					'preserveAlpha' => $transparency,
					'preserveTransparency' => $transparency,
					'jpegQuality'	=> $quality,
						) );

		if ( false === $crop )
		{
			$thumb->resize( $width, $height );
		}
		else
		{
			$thumb->adaptiveResize( $width, $height, $crop['x'], $crop['y'] );
		}

		$thumb->save( $to, $fileinfo['extension'] );

		return true;
	}

	/**
	 * Crop an image using specific points where the crop have to begin.
	 *
	 * @param $from Origin file name
	 * @param $to Final file name
	 * @param $startX X point where the crop have to begin.
	 * @param $startY Y point where the crop have to begin.
	 * @param $width Final width.
	 * @param $height Final height
	 * @param bool $resizeUp
	 * @param bool $transparency
	 * @param int $quality
	 * @return bool
	 */
	static public function cropAndSave( $from, $to, $startX, $startY, $width, $height, $resizeUp = false, $transparency = false, $quality = 100 )
	{
		include_once ROOT_PATH . '/libs/' . Config::getInstance()->getLibrary( 'phpthumb' ) . '/ThumbLib.inc.php';

		$fileinfo = pathinfo( $to );

		$thumb = \PhpThumbFactory::create( $from );
		$thumb = \PhpThumbFactory::create( $from, array(
					'resizeUp' => $resizeUp,
					'preserveAlpha' => $transparency,
					'preserveTransparency' => $transparency,
					'jpegQuality'	=> $quality,
						) );

		$thumb->crop( $startX, $startY, $width, $height );

		$thumb->save( $to, $fileinfo['extension'] );

		return true;
	}

	/**
	 * Upload and resize an image.
	 *
	 * @param file $from
	 * @param file $to
	 * @param integer $width
	 * @param integer $height
	 * @param boolean $crop
	 * @return boolean
	 */
	static public function uploadResizeAndSave( $post_file, $destination, $width, $height, $crop = false, $resizeUp = false, $transparency = false )
	{
		$old_name = $post_file['tmp_name'];
		$upload_info = pathinfo( $old_name );
		$new_name = $upload_info['dirname'].'/'.$post_file['name'];

		move_uploaded_file( $old_name, $new_name );

		self::resizeAndSave( $new_name, $destination, $width, $height, $crop, $resizeUp, $transparency );

		return true;
	}
}