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
 * Configuration file parser.
 */
class Config
{

	/**
	 * Defines what profile do you want to use to load libraries. This must be a key in domains.config.php
	 * @var string
	 */
	static public $libraries_profile = 'default';

	/**
	 * Singleton instance.
	 *
	 * @var	Config
	 */
	static protected $instance;

	/**
	 * File used as base to load the paths to configuration files.
	 *
	 * @var string
	 */
	protected $configuration_files = 'configuration_files.config.php';

    /**
     * Variables taken from the config are stored here in the class context.
     *
     * @var array
     */
    protected $config_values;

	/**
	 * Content inside the $configuration_files file specififying in which instance is found every config.
	 *
	 * @var unknown_type
	 */
	protected $paths_to_configs = array();

	protected function __construct( $instance_name )
	{
		$this->instance_name = $instance_name;
		if ( $instance_name === 'tests' )
		{
			$this->config_path = ROOT_PATH . '/vendor/sifophp/sifo-common-instance/config/';
		}
		else
		{
			$this->config_path = ROOT_PATH . "/instances/" . $instance_name ."/config/";
		}

		include( $this->config_path . $this->configuration_files );
		$this->paths_to_configs = $config;
	}

	/**
	 * Singleton of config class.
	 *
	 * @param string $instance_name Instance Name, needed to determine correct paths.
	 * @return Config
	 */
	public static function getInstance( $instance_name = null )
	{
		// Load instance from bootsrap
		if ( !isset( $instance_name ) )
		{
			$instance_name = Bootstrap::$instance;
		}

		if ( !isset ( self::$instance[$instance_name] ) )
		{
			self::$instance[$instance_name] = new self( $instance_name );
		}

		return self::$instance[$instance_name];
	}

	/**
	 * Loads the desired config file for a given valid 'profile'.
	 *
	 * @param string $profile The requested profile.
	 * @throws Exception_Configuration When isn't set the self::PROFILE_NAME_FOR_CONFIG_FILES section or the desired profile.
	 * @return boolean
	 */
	protected function loadConfig( $profile )
	{
		if( !isset( $this->paths_to_configs[$profile] ) )
		{
			throw new Exception_Configuration( "The profile '$profile' was not found" );
		}
		else
		{
			if ( !include( ROOT_PATH . '/' . $this->paths_to_configs[$profile] ) )
			{
				throw new Exception_Configuration( "Failed to include file " . ROOT_PATH . '/' . $this->paths_to_configs[$profile] , E_USER_ERROR );
			}
			else
			{
				// The file was correctly included. We include the variable $config found.
				if ( !isset( $config ) )
				{
					throw new Exception_Configuration( 'The configuration files must have a variable named $config' );
				}

				return $config;
			}
		}
	}

	/**
	 * Gets the profile config variables for the desired profile.
	 *
	 * @param string $profile The requested profile.
	 * @param string $group The requested group inside the profile.
	 * @throws Exception_Configuration When the selected group or profile doesn't exist.
	 * @return mixed $config_values The config values in the config file of the current profile.
	 */
	public function getConfig( $profile, $group = null )
	{
		if ( !isset( $this->config_values[$profile] ) )
		{
			$this->config_values[$profile] = $this->loadConfig( $profile );
		}

		if ( is_null( $group ) )
		{
			return $this->config_values[$profile];
		}
		if ( isset( $this->config_values[$profile][$group] ) )
		{
			return $this->config_values[$profile][$group];
		}

		throw new Exception_Configuration( "The group '$group' for profile '$profile' was never set.", E_USER_ERROR );
	}

	/**
	 * Given a class name, returns the final class name and path to file.
	 *
	 * @throws Exception_Configuration When the requested class doesn't exist in the .classes file.
	 * @param string $class_type The desired KEY in the configuration file.
	 * @return mixed Array with final name class and path.
	 */
	public function getClassInfo( $class_type )
	{
		$classes = $this->getConfig( 'classes' );
		$class_type = explode( '\\', $class_type );
        $path = null;

		if ( isset( $class_type[1] ) && $class_type[0] == '\\' . $class_type[1] )
		{
			unset( $class_type[1] );
		}

		// Append the Namespace on an existing classes.config class.
		if ( isset( $classes[$class_type[0]] ) && !isset( $class_type[1] ) )
		{
			$instances = array_keys( $classes[$class_type[0]] );
			$last_instance = array_pop( $instances );
			array_push( $class_type, $last_instance );
			$path = array_pop( $classes[$class_type[0]] );
		}
		elseif( isset( $class_type[1] ) )
		{
			$class_type = array_reverse( $class_type );
			$path = $classes[$class_type[0]][$class_type[1]];
		}

		if ( isset( $classes[$class_type[0]] ) && !isset( $path ) )
		{
			$path = array_pop( $classes[$class_type[0]] );
		}

		if ( !isset( $classes[$class_type[0]] ) )
		{
			// Error handling.
			throw new Exception_Configuration( "The variable '{$class_type[0]}' was not found in the classes file. ", E_USER_ERROR );
		}

		// The var is OK,  we return the requested array element.
		$class_name = "\\{$class_type[1]}\\$class_type[0]";
		return array( 'name' => $class_name, 'path' => $path );
	}

	/**
	 * Instance name.
	 *
	 * @return string
	 */
	public function getInstanceName()
	{
		return $this->instance_name;
	}

	/**
	 * Returns the library assigned to the given alias.
	 *
	 * @param string $alias Alias of the library, e.g: 'smarty'
     * @return string Effective name of the folder with the library
     */
	public function getLibrary( $alias )
	{
		$libraries = $this->getConfig( 'libraries', 'default' );

		// User requested a different profile, combine with default for missing attributes.
		if ( self::$libraries_profile != 'default' )
		{
			$libraries = array_merge( $libraries, $this->getConfig( 'libraries', self::$libraries_profile ) );
		}

		if ( !isset( $libraries[$alias] ) )
		{
			throw new Exception_Configuration( "The library '$alias' you are loading is not set in profile " . self::$libraries_profile );
		}

		return $libraries[$alias];
	}
}

/**
 * Exception for the process.
 */
class Exception_Configuration extends \Exception
{
}

?>
