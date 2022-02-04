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

use PhpThumbFactory;

class Images
{
	/**
	 * Resize an image.
	 *
	 * @param string $from
	 * @param string $to
	 * @param integer $width
	 * @param integer $height
	 * @param false|array $crop
	 * @param boolean $resizeUp
	 * @param boolean $transparency
	 * @param int $quality
	 * @return boolean
	 */
	public static function resizeAndSave(
        string $from,
        string  $to,
        int $width,
        int $height,
        array $crop = [],
        bool $resizeUp = false,
        bool $transparency = false,
        int $quality = 100
    ) {
		$fileinfo = pathinfo($to);

		$thumb = PhpThumbFactory::create(
            $from,
            [
					'resizeUp' => $resizeUp,
					'preserveAlpha' => $transparency,
					'preserveTransparency' => $transparency,
					'jpegQuality'	=> $quality,
            ]
        );

		if (self::isCropRequired($crop)) {
            $thumb->adaptiveResize($width, $height, $crop['x'], $crop['y']);
		} else {
            $thumb->resize($width, $height);
		}

		$thumb->save($to, $fileinfo['extension']);

		return true;
	}

	/**
	 * Crop an image using specific points where the crop have to begin.
	 *
	 * @param string $from Origin file name
	 * @param string $to Final file name
	 * @param $startX X point where the crop have to begin.
	 * @param $startY Y point where the crop have to begin.
	 * @param $width Final width.
	 * @param $height Final height
	 * @param bool $resizeUp
	 * @param bool $transparency
	 * @param int $quality
	 * @return bool
	 */
	static public function cropAndSave(
        string $from,
        string $to,
        int $startX,
        int $startY,
        int $width,
        int $height,
        bool $resizeUp = false,
        bool $transparency = false,
        int $quality = 100
    ) {
		$fileInfo = pathinfo($to);

        $thumb = \PhpThumbFactory::create(
            $from,
            [
                'resizeUp' => $resizeUp,
                'preserveAlpha' => $transparency,
                'preserveTransparency' => $transparency,
                'jpegQuality' => $quality,
            ]
        );

		$thumb->crop($startX, $startY, $width, $height);

		$thumb->save($to, $fileInfo['extension']);

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
	static public function uploadResizeAndSave(
        array $post_file,
        string $destination,
        int $width,
        int $height,
        array $crop = [],
        bool $resizeUp = false,
        bool $transparency = false
    ) {
		$old_name = $post_file['tmp_name'];
		$upload_info = pathinfo( $old_name );
		$new_name = $upload_info['dirname'].'/'.$post_file['name'];
        static::moveFile($old_name, $new_name);

		self::resizeAndSave($new_name, $destination, $width, $height, (array)$crop, $resizeUp, $transparency);

		return true;
	}

    private static function isCropRequired(array $crop): bool
    {
        return array_key_exists('x', $crop)
            && is_int($crop['x'])
            && array_key_exists('y', $crop)
            && is_int($crop['y']);
    }

    protected static function moveFile(string $from, string $to): void
    {
        move_uploaded_file($from, $to);
    }
}
