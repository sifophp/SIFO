<?php

namespace Common;
/**
 * A rebuild for router when no database is used.
 *
 * Keeps the router_xx_XX files syncronized with the router_en_US or whatever you set your master file.
 */
class ManagerRebuildRouterController extends \Sifo\Controller
{
	const MASTER_LANGUAGE = 'en_US';

	public function build()
	{
		header( 'Content-Type: text/plain' );

		$this->setLayout( 'manager/findi18n.tpl' );

		try
		{
			$master_routes = \Sifo\Config::getInstance( $this->instance )->getConfig( 'lang/router_' . self::MASTER_LANGUAGE );
		}
		catch( Exception_Configuration $e )
		{
			die( 'The master file does not exist. ' . $e->getMessage() );
		}

		$findI18N = new ManagerFindi18nController();
		$files_available = $findI18N->getFilesystemFiles( "instances/{$this->instance}/config/lang" );

		foreach ( $files_available as $key => $filename )
		{
			// Is a 'router' config file (but not master)
			if ( strpos( $filename, 'router_' ) !== false  )
			{
				$translated_routes = $this->getTranslatedRoutes( $filename );

				// Remove keys not present in master:
				foreach( $translated_routes as $route => $route_translated )
				{
					if ( !isset( $master_routes[$route] ) )
					{
						unset( $translated_routes[$route] );
						echo "Deleted route $route in $filename\n";
					}
				}



				// Add keys not present in master
				foreach( $master_routes as $route => $route_translated )
				{
					if ( !isset( $translated_routes[$route] ) )
					{
						$translated_routes[$route] = $route_translated;
					}
				}

				ksort( $translated_routes );
				$this->saveConfig($filename, $translated_routes);
			}
		}

		echo "\n\nRoutes rebuild!";
		die;
	}

	protected function saveConfig( $filename, $values )
	{
		$config_file = \Sifo\Bootstrap::$application . "/{$this->instance}/config/lang/$filename";
		file_put_contents( $config_file, "<?php

namespace Common;\n"
				. '$config = ' . var_export( $values, true ) . ';' );
	}

	protected function getTranslatedRoutes( $filename )
	{
		$path = \Sifo\Bootstrap::$application . "/{$this->instance}/config/lang/$filename";

		include "$path";
		ksort( $config );

		return $config;
	}
}