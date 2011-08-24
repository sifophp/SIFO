<?php

class ManagerRebuildController extends Controller
{

	/**
	 * Filenames where the configuration files will be stored.
	 * @var string
	 */
	protected $filenames = array(
		'config' => 'configuration_files.config.php',
		'templates' => 'templates.config.php',
		'classes' => 'classes.config.php'
	);

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

		foreach ( $files as $file => $folders )
		{
			$configs = array( );
			foreach ( $folders as $folder )
			{
				$configs = array_merge( $configs, $this->getAvailableFiles( $folder ) );
			}

			$this->assign( 'config', $configs );
			$configs_content = $this->grabHtml();
			file_put_contents( ROOT_PATH . "/instances/" . $this->instance . "/config/" . $this->filenames[$file], $configs_content );
			$output[$file] = $configs_content;
		}

		return $output;

	}

	public function build()
	{
		// Cannot use Iterators yet. PHP too old.
		$this->getClass( 'Dir' );

		if ( true !== $this->hasDebug() )
		{
			throw new Exception_404( 'User tried to access the rebuild page, but he\'s not in development' );
		}




		// Calculate where the config files are taken from.
		$files_output = $this->rebuildFiles( array(
			'config' => array( 'config' ),
			'templates' => array( 'templates' ),
			'classes' => array( 'core', 'classes', 'controllers', 'models' ),
				) );


		// Reset the layout and paste the content in the empty template:
		$this->setLayout( 'empty.tpl' );
		// Disable debug on this page.
		$this->setDebug( false );
		$message = <<<MESG
INSTANCE '{$this->instance}'.
MESG;
		foreach ( $files_output as $file => $output )
		{
			$message .= "\n==== {$this->filenames[$file]} ====\n$output\n\n";
		}

		$this->assign( 'content', $message );


		header( 'Content-Type: text/plain' );

	}

	protected function getRunningInstances()
	{
		$d = new Dir();
		$instances = $d->getDirs( ROOT_PATH . '/instances' );

		return $instances;

	}

	private function cleanStartingSlash( $path )
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
	private function getClassStandardized( $path )
	{
		$class = '';

		$ctrl_parts = explode( '/', $path );

		while ( $class_name = array_shift( $ctrl_parts ) )
		{
			$class .= ucfirst( $class_name );
		}

		return $class;

	}

	protected function getAvailableFiles( $type )
	{
		$d = new Dir();
		$type_files = array( );

		$core_inheritance = Domains::getInstance()->getCoreInheritance();
		$instance_inheritance = Domains::getInstance()->getInstanceInheritance();

		if ( $type == 'core' )
		{
			foreach ( $core_inheritance as $corelib )
			{
				$available_files = $d->getFileListRecursive( ROOT_PATH, '/libs/' . $corelib );
				if ( count( $available_files ) > 0 )
				{
					foreach ( $available_files as $k => $v )
					{
						// Allow only extensions PHP, TPL, CONF
						$desired_file_pattern = preg_match( "/\.(php|tpl|conf)$/i", $v["relative"] );

						if ( $desired_file_pattern )
						{
							$rel_path = $this->cleanStartingSlash( $v["relative"] );
							$path = $rel_path;
							$rel_path = str_replace( 'libs/' . $corelib . '/', '', $rel_path );
							$rel_path = str_replace( 'libs/SEOWrappers/', '', $rel_path );
							$rel_path = str_replace( '.php', '', $rel_path ); // Default

							$class = $this->getClassStandardized( $rel_path );
							$type_files[$class] = $class . '::' . $path;
						}
					}
				}
			}
		}
		else
		{
			foreach ( $instance_inheritance as $current_instance )
			{
				$available_files = $d->getFileListRecursive( ROOT_PATH . "/instances/" . $current_instance . "/$type" );

				if ( is_array( $available_files ) === true && count( $available_files ) > 0 )
				{
					foreach ( $available_files as $k => $v )
					{
						$rel_path = $this->cleanStartingSlash( $v["relative"] );
						$class = '';

						$path = str_replace( '//', '/', "instances/$current_instance/$type/$rel_path" );

						// Calculate the class name for the given file:
						$rel_path = str_replace( '.model.php', '', $rel_path );
						$rel_path = str_replace( '.ctrl.php', '', $rel_path );
						$rel_path = str_replace( '.config.php', '', $rel_path );
						$rel_path = str_replace( '.php', '', $rel_path ); // Default

						$class = $this->getClassStandardized( $rel_path );

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
								$class_extended .= 'Controller';
								$type_files[$class] = $class_extended . '::' . $path;
								break;
							case 'models':
								$class .= 'Model';
								$class_extended .= 'Model';
								$type_files[$class] = $class_extended . '::' . $path;
								break;
							case 'classes':
								$type_files[$class] = $class_extended . '::' . $path;
								break;
							case 'templates':
							case 'config':
							default:
								$type_files[$rel_path] = $path;
								}
							  }
						}
					}
				}


		ksort( $type_files );

		return $type_files;

	}

}
?>