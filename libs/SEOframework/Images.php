<?php
class Images
{
	/**
	 * Resize an image.
	 *
	 * @param file $from
	 * @param file $to
	 * @param integer $width
	 * @param integer $height
	 * @param boolean $crop
	 * @param boolean $transparency
	 * @return boolean
	 */
	static public function resizeAndSave( $from, $to, $width, $height, $crop = false, $resizeUp = false, $transparency = false, $quality = 100 )
	{
		include_once ROOT_PATH . '/libs/' . Config::getInstance()->getLibrary( 'phpthumb' ) . '/ThumbLib.inc.php';

		$fileinfo = pathinfo( $to );
	
		$thumb = PhpThumbFactory::create( $from );
		$thumb = PhpThumbFactory::create( $from, array(
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
			$thumb->adaptiveResize( $width, $height );
		}
		
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