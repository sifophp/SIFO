<?php
class ManagerRebuildController extends Controller
{
	public function build()
	{
		$this->getClass( 'Dir' );

		if ( true !== $this->hasDebug() )
		{
			throw new Exception_404( 'User tried to access the rebuild page, but he\'s not in development' );
		}

		$this->setLayout( 'manager/templates.tpl' );

		// Calculate where the config files are taken from.
		$configs = $this->getAvailableFiles( 'config' );
		$this->assign( 'config', $configs );
		$configs_content = $this->grabHtml();
		file_put_contents( ROOT_PATH . "/instances/" . $this->instance . "/config/configuration_files.config.php", $configs_content );

		// Calculate where the templates are taken from
		$templates = $this->getAvailableFiles( 'templates' );
		$this->assign( 'config', $templates );
		$template_content = $this->grabHtml();
		file_put_contents( ROOT_PATH . "/instances/" . $this->instance . "/config/templates.config.php", $template_content );

		// Calculate where the controllers, models and unsuitable classes are taken from.
		$controllers = $this->getAvailableFiles( 'controllers' );
		$core = $this->getAvailableFiles( 'core' );
		$models = $this->getAvailableFiles( 'models' );
		$classes = $this->getAvailableFiles( 'classes' );
		$classes = array_merge( $core, $classes, $controllers, $models );
		$this->assign( 'config', $classes );
		$classes_content = $this->grabHtml();
		file_put_contents( ROOT_PATH . "/instances/" . $this->instance . "/config/classes.config.php", $classes_content );

		// Reset the layout and paste the content in the empty template:
		$this->setLayout( 'empty.tpl' );
		// Disable debug on this page.
		$this->setDebug( false );
		$message = <<<MESG
INSTANCE '{$this->instance}'.
templates.config.php
====================
				$template_content

classes.config.php
====================
				$classes_content

configuration_files.config.php
====================
				$configs_content
MESG;
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

		while( $class_name = array_shift( $ctrl_parts ) )
		{
			$class .= ucfirst( $class_name );
		}

		return $class;
	}

	protected function getAvailableFiles( $type )
	{
		$d = new Dir();
		$type_files = array();

		//TODO: Poner en config.
		$core_inheritance = Domains::getInstance()->getCoreInheritance();
		$instance_inheritance = Domains::getInstance()->getInstanceInheritance();

		if ( $type == 'core')
		{
			foreach( $core_inheritance as $corelib )
			{
				$available_files = $d->getFileListRecursive( ROOT_PATH, '/libs/' . $corelib );
				if ( count( $available_files ) > 0 )
				{
					foreach ( $available_files as $k => $v )
					{
						// Allow only extensions PHP, TPL, CONF
						$desired_file_pattern = preg_match( "/\.(php|tpl|conf)$/i" , $v["relative"]) ;

						if ( $desired_file_pattern )
						{
							$rel_path = $this->cleanStartingSlash( $v["relative"] );
							$path = $rel_path;
							$rel_path = str_replace( 'libs/' . $corelib . '/', '', $rel_path );
							$rel_path = str_replace( 'libs/SEOWrappers/', '', $rel_path );
							$rel_path = str_replace( '.php', '', $rel_path ); // Default

							$class = $this->getClassStandardized( $rel_path );
							$type_files[$class] = $class .'::' . $path;
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


						switch( $type )
						{
							case 'controllers':
								$class .= 'Controller';
								$class_extended .= 'Controller';
								$type_files[$class] =  $class_extended .'::' . $path;
								break;
							case 'models':
								$class .= 'Model';
								$class_extended .= 'Model';
								$type_files[$class] =  $class_extended .'::' . $path;
								break;
							case 'classes':
								$type_files[$class] = $class_extended .'::' . $path;
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