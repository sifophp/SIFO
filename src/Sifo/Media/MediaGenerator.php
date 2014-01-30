<?php
/**
 * LICENSE
 *
 * Copyright 2010 Carlos Soriano
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

namespace Sifo\Media;

use Sifo\Config;
use Sifo\Domains;
use Sifo\Exception\ConfigurationException;

/**
 * Generates media files.
 */
class MediaGenerator
{
	/**
	 * Name of the current instance.
	 *
	 * @var string
	 */
	protected $working_instance;

	/**
	 * Store current instance inheritance.
	 *
	 * @var array
	 */
	protected $instance_inheritance;

	/**
	 * Code language of the current instance.
	 *
	 * @var string
	 */
	protected $instance_language;

	/**
	 * Static path of the current instance.
	 *
	 * @var string
	 */
	protected $static_path;

	/**
	 * Store the media config of all the current instance tree.
	 *
	 * @var array
	 */
	protected $media_config = array();

	/**
	 * Media type, specified in the children.
	 *
	 * @var string
	 */
	protected $media_type;

	/**
	 * Generates all groups, and not only the ones added by the controller.
	 *
	 * @var boolean
	 */
	protected $generate_all_groups = false;

	/**
	 * The list of already generated media groups.
	 *
	 * @var array
	 */
	protected $generated_groups = array();

	/**
	 * The added media list to be generated.
	 *
	 * @var array
	 */
	protected static $added_media = array();

	/**
	 * Class constructor.
	 *
	 * This parent class can't be instanciated.
	 */
	public function __construct()
	{
		throw new \Exception( 'This class can\'t be instanciated, please specify one of the children' );
	}

	/**
	 * Set attributes.
	 */
	protected function setInstance()
	{
		$this->working_instance = Config::getInstance()->getInstanceName();
		$this->instance_inheritance = Domains::getInstance()->getInstanceInheritance();
		$this->instance_language = substr( Domains::getInstance()->getLanguage(), 0, 2 );
		$this->instance_static_host = Domains::getInstance()->getStaticHost();
		$this->static_path = ROOT_PATH . '/instances/' . $this->working_instance . '/public/static/';
		$this->hashes_file = $this->static_path . $this->media_type . '.hashes.php';

		foreach ( $this->instance_inheritance as $instance )
		{
			try
			{
				$instance_config = Config::getInstance( $instance )->getConfig( $this->media_type );
				$this->media_config = array_merge( $this->media_config, $instance_config );
			}
			catch( ConfigurationException $e )
			{
				// Config not found. This is OK.
			}
		}
	}

	/**
	 * Add JS to the stack.
	 *
	 * @param string $media_name Name of the JS file.
	 */
	public static function addJs( $media_name )
	{
		self::addMedia( 'js', $media_name );
	}

	/**
	 * Add CSS to the stack.
	 *
	 * @param string $media_name Name of the CSS file.
	 */
	public static function addCss( $media_name )
	{
		self::addMedia( 'css', $media_name );
	}

	/**
	 * Add some kind of media to the stack to be loaded in the head.
	 *
	 * @param string $media_type Media type [js|css].
	 * @param string $group_name Name of the group in the js|css config file.
	 */
	protected static function addMedia( $media_type, $group_name )
	{
		$media_config = Config::getInstance()->getConfig( $media_type );
		if ( isset( $media_config['packages'][$group_name] ) )
		{
			self::$added_media[$media_type][key( $media_config['packages'][$group_name] )] = $group_name;
			ksort( self::$added_media[$media_type] );
		}
		else
		{
			trigger_error( 'The specified group name "' . $group_name . '" does not exists in config file', E_USER_WARNING );
		}
	}

	/**
	 * Returns the added media.
	 *
	 * @return array
	 */
	public static function getMedia()
	{
		return self::$added_media;
	}

	/**
	 * Get the link to the generated media.
	 *
	 * @param array $media List of media files.
	 * @return array
	 */
	public function getGenerated( Array $media )
	{
		if ( !( $generated = $this->getGeneratedHashes() ) )
		{
			$base_code = '';
			if ( $this->generate_all_groups === true )
			{
				$base_code = $this->generateAllMediaGroups();
			}

			$media = $this->getMediaArray( $media );

			$generated = array();
			foreach ( $media as $type => $media_list )
			{
				if ( !in_array( $type, $this->generated_groups ) )
				{
					$generated[$type] = $this->generatePackedFile( $media_list, $base_code );
				}
			}

			$content = $this->getHashesFileContent( $generated );
			file_put_contents( $this->hashes_file, $content );
		}

		return $generated;
	}

	protected function getMediaArray( array $media )
	{
		$media_array = array();
		foreach( $media as $group )
		{
			$media_config = $this->media_config['packages'][$group];
			$media_array[$group] = $this->buildMediaArray( $media_config );
		}

		return $media_array;
	}

	protected function buildMediaArray( array $media_config )
	{
		$filenames = array();
		foreach ( $media_config as $relationship )
		{
			if ( isset( $relationship['package'] ) )
			{
				throw new \Exception( 'Package grouping not supported yet!' );
			}
			else
			{
				$filenames[] = $this->media_config['files'][$relationship['file']];
			}
		}

		return $filenames;
	}

	protected function getGeneratedHashes()
	{
		if ( Domains::getInstance()->getDevMode() )
		{
			return false;
		}

		$hashes = false;

		include_once $this->hashes_file;

		if ( !$this->isGeneratedFilesUpToDate( $revision ) )
		{
			return false;
		}

		return $hashes;
	}

	protected function isGeneratedFilesUpToDate( $revision )
	{
		$checkout_revision = $this->getCheckoutRevision();

		return ( $checkout_revision === $revision );
	}

	public static function getCheckoutRevision( $root_path = ROOT_PATH )
	{
		$revision = rtrim( file_get_contents( $root_path . '/.git/HEAD' ) );
		if ( strpos( $revision, 'ref: ' ) === 0 )
		{
			$revision_path = substr( $revision, 5 );
			$revision = rtrim( file_get_contents( $root_path . '/.git/' . $revision_path ) );
		}

		return $revision;
	}

	protected function getHashesFileContent( Array $generated )
	{
		$revision = $this->getCheckoutRevision();
		return "<?php\n\$revision='{$revision}';\n\$hashes=" . var_export( $generated, true ) . ';';
	}

	protected function generateAllMediaGroups()
	{
		$media = array();
		foreach( $this->media_config['packages'] as $group => $media_config )
		{
			$media[$group] = $this->buildMediaArray( $media_config );
		}

		foreach( $media as $group => $media_name )
		{
			if ( $group !== 'common' )
			{
				ksort( $media[$group] );
				$this->generated_groups[] = $group;
				$generated_files[] = $this->generatePackedFile( $media[$group] );
				$generated_media[$group] = $media_name;
			}
		}

		return $this->getBaseCode( $generated_media, $generated_files );
	}

	/**
	 * Gets the code that will be embeeded in the head JS file.
	 *
	 * This code relates module names to the generated js file that contains the code.
	 *
	 * @param array $media_list The list of media files that has been generated.
	 * @param array $generated_files The resultant generated files.
	 * @return string
	 */
	protected function getBaseCode( Array $media_list, Array $generated_files )
	{
		return '';
	}

	/**
	 * Generates the final packaged and optionally compressed file that will be loaded in the browser.
	 *
	 * @param array $media_list List of media files.
	 * @param string $preffix_code Code to be added at the start of the generated file.
	 * @return string
	 */
	protected function generatePackedFile( Array $media_list, $preffix_code = '' )
	{
		$current_hash = $this->getMediaHash( $media_list );
		$list_hash = md5( implode( '-', $media_list ) );

		$file = $this->media_type . '/generated/' . $list_hash . '-' . $current_hash . '.' . $this->media_type;
		$path = $this->static_path . $file;
		$generated_file = $this->instance_language . '/' . $this->media_type . '/' . $list_hash . '-' . $current_hash . '.' . $this->media_type;

		$this->generateMedia( $path, $media_list, $preffix_code );

		return $generated_file;
	}

	/**
	 * Generate the media file if it doesn't already exist.
	 *
	 * @param string $dest_filename Filename of the generated media.
	 * @param array $media_list List of media files.
	 * @param string $preffix_code Code to be added at the start of the generated file.
	 */
	protected function generateMedia( $dest_filename, Array $media_list, $preffix_code = '' )
	{
		if ( is_file( $dest_filename ) )
		{
			return false;
		}

		$content = $preffix_code;
		foreach ( $media_list as $media )
		{
			foreach ( $this->instance_inheritance as $instance_name )
			{
				$filename = $this->getStaticPath( $instance_name, $media );

				if ( is_file( $filename ) )
				{
					$content .= file_get_contents( $filename ) . chr( 13 );
				}
			}
		}

		$content = $this->parseContent( $content );

		file_put_contents( $dest_filename, $content );
	}

	/**
	 * Get the hash tag of a given media file list.
	 *
	 * @param array $media_list List of media files.
	 * @return string
	 */
	protected function getMediaHash( Array $media_list )
	{
		$hash = '';
		foreach ( $media_list as $media )
		{
			foreach ( $this->instance_inheritance as $instance_name )
			{
				$filename = $this->getStaticPath( $instance_name, $media );

				if ( is_file( $filename ) )
				{
					$hash .= md5_file( $filename );
				}
			}
		}

		return md5( $hash );
	}

	/**
	 * Get the full path of a media file in a defined instance.
	 *
	 * @param string $instance Instance name.
	 * @param string $filename Media filename.
	 * @return string
	 */
	protected function getStaticPath( $instance, $filename )
	{
		return ROOT_PATH . '/instances/' . $instance . '/public/static/' . $this->media_type . '/' . $filename;
	}

	/**
	 * Parse the media to add special content.
	 *
	 * Specify this method in the children.
	 *
	 * @param string $content File content.
	 * @return string
	 */
	protected function parseContent( $content )
	{
		return $content;
	}
}