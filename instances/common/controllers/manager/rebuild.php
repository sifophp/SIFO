<?php

namespace Common;

class ManagerRebuildController extends \Sifo\Controller
{

	/**
	 * Filenames where the configuration files will be stored.
	 * @var string
	 */
	protected $filenames = array(
		'config' => 'configuration_files.config.php',
		'templates' => 'templates.config.php',
		'classes' => 'classes.config.php',
		'locale' => 'locale.config.php'
	);

	/**
	 * Saves files that couldn't be saved to disk.
	 *
	 * @var array
	 */
	protected $failed_files = array();

	/**
	 * Writes all the configurattion files to disk.
	 *
	 * Input expected is:
	 *
	 * array( 'filename' => array( 'folder_to_parse1', 'folder_to_parse2', '...' ) )
	 *
	 * @param array $files
	 * @return array Array of contents write to each file.
	 */
	protected function rebuildFiles( Array $files )
	{
		$this->setLayout( 'manager/templates.tpl' );

		$output = array( );

		$instance_inheritance 	= array_unique( \Sifo\Domains::getInstance()->getInstanceInheritance() );

		$instance_inheritance_reverse = array_reverse( $instance_inheritance );

		// Build the instance configuration: instance name and his parent instance name is exists.
		foreach( $instance_inheritance_reverse as $key => $instance )
		{
			$instance_config['current'] 	= $instance;
			if ( isset( $instance_inheritance[ $key+1 ] ) )
			{
				$instance_config['parent'] 	= $instance_inheritance_reverse[$key+1];
			}
			$instances_configuration[] = $instance_config;
			unset( $instance_config );
		}

		// For each instance in the inheritance it regenerates his configuration files.
		foreach( $instances_configuration as $instance )
		{
			$current_instance		= $instance['current'];

			$this->assign( 'instance_parent', null );
			if ( isset( $instance['parent'] ) )
			{
				$this->assign( 'instance_parent', $instance['parent'] );
			}

			foreach ( $files as $file => $folders )
			{
				$configs = array( );
				foreach ( $folders as $folder )
				{
					$configs = array_merge( $configs, $this->getAvailableFiles( $folder, $current_instance ) );
				}

				$this->assign( 'config', $configs );
				$this->assign( 'file_name', $this->filenames[$file] );

				$configs_content = $this->grabHtml();
				$file_destination = ROOT_PATH . "/instances/" . $current_instance . "/config/" . $this->filenames[$file];
				$success = file_put_contents( $file_destination, $configs_content );
				if ( !$success )
				{
					$this->failed_files[] = $file_destination;
				}
				$output[$current_instance][$file] = $configs_content;
			}
		}

		return $output;

	}

	public function build()
	{
		if ( true !== \Sifo\Domains::getInstance()->getDevMode() )
		{
			throw new \Sifo\Exception_404( 'User tried to access the rebuild page, but he\'s not in development' );
		}

		// Calculate where the config files are taken from.
		$files_output = $this->rebuildFiles( array(
			'config' => array( 'config' ),
			'templates' => array( 'templates' ),
			'classes' => array( 'core', 'classes', 'controllers', 'models' ),
			'locale' => array( 'locale' ),
		) );

		// Reset the layout and paste the content in the empty template:
		$this->setLayout( 'manager/rebuild.tpl' );
		// Disable debug on this page.
		\Sifo\Domains::getInstance()->setDebugMode( false );

		$this->assign( 'inheritance', array_reverse( array_unique( \Sifo\Domains::getInstance()->getInstanceInheritance() ) ) );

		$this->assign( 'errors', $this->failed_files );
		$this->assign( 'filenames', $this->filenames );
		$this->assign( 'files_output', $files_output );
	}

	protected function getRunningInstances()
	{
		$d = new \Sifo\Dir();
		$instances = $d->getDirs( ROOT_PATH . '/instances' );

		return $instances;

	}

	protected function cleanStartingSlash( $path )
	{
		if ( 0 === strpos( $path, "/" ) )
		{
			// Remove starting slashes.
			return substr( $path, 1 );
		}
		return $path;

	}

	/**
	 * Converts something like home/index.ctrl.php to HomeIndex.
	 *
	 * @param string $path
	 * @return string
	 */
	private function getClassTypeStandarized( $path )
	{
		$class = '';

		$ctrl_parts = explode( '/', $path );

		while ( $class_name = array_shift( $ctrl_parts ) )
		{
			$class .= ucfirst( $class_name );
		}

		return $class;
	}

	protected function getAvailableFiles( $type, $current_instance )
	{
		$d = new \Sifo\Dir();
		$type_files = array( );

		$core_inheritance = \Sifo\Domains::getInstance()->getCoreInheritance();

		if ( $type == 'core' )
		{
			foreach ( $core_inheritance as $corelib )
			{
				$available_files = $d->getFileListRecursive( ROOT_PATH, '/libs/' . $corelib );
				if ( count( $available_files ) > 0 )
				{
					foreach ( $available_files as $v )
					{
						// Allow only extensions PHP, TPL, CONF
						$desired_file_pattern = preg_match( '/\.(php|tpl|conf)$/i', $v["relative"] );

						if ( $desired_file_pattern )
						{
							$rel_path = $this->cleanStartingSlash( $v["relative"] );
							$path = $rel_path;
							$rel_path = str_replace( 'libs/' . $corelib . '/', '', $rel_path );
							$rel_path = str_replace( '.php', '', $rel_path ); // Default

							$class = $this->getClassTypeStandarized( $rel_path );
							$type_files[$class]['Sifo'] = $path;
						}
					}
				}
			}
		}
		else
		{
			$available_files = $d->getFileListRecursive( ROOT_PATH . "/instances/" . $current_instance . "/$type" );

			if ( is_array( $available_files ) === true && count( $available_files ) > 0 )
			{
				foreach ( $available_files as $k => $v )
				{
					$rel_path = $this->cleanStartingSlash( $v["relative"] );

					$path = str_replace( '//', '/', "instances/$current_instance/$type/$rel_path" );

					// Calculate the class name for the given file:
					$rel_path = str_replace( '.model.php', '', $rel_path );
					$rel_path = str_replace( '.ctrl.php', '', $rel_path );
					$rel_path = str_replace( '.config.php', '', $rel_path );
					$rel_path = str_replace( '.php', '', $rel_path ); // Default

					$class = $this->getClassTypeStandarized( $rel_path );

					if ( 'default' != $current_instance )
					{
						$class_extended = $class . ucfirst( $current_instance );
					}
					else
					{
						$class_extended = $class;
					}

					switch ( $type )
					{
						case 'controllers':
							$class .= 'Controller';
							$type_files[$class][ucfirst( $current_instance )] = $path;
							break;
						case 'models':
							$class .= 'Model';
							$type_files[$class][ucfirst( $current_instance )] = $path;
							break;
						case 'classes':
							$type_files[$class][ucfirst( $current_instance )] = $path;
							break;
						case 'config':
							if ( $rel_path == 'configuration_files' )
							{
								continue;
							}
						case 'templates':
						default:
							$type_files[$rel_path] = $path;
					}
				}
			}
		}

		ksort( $type_files );
		return $type_files;
	}

}
?>