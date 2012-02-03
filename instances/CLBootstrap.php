<?php
/**
 *
 * Class CLBootstrap
 */
namespace Sifo;

require_once 'Bootstrap.php';

class CLBootstrap extends Bootstrap
{
	static $script_controller;
	static $command_line_params ;

	/**
	 * Starts the execution. Root path is passed to avoid recalculation.
	 *
	 */
	public static function execute()
	{
		// Register autoloader:
		spl_autoload_register( array( '\\Sifo\Bootstrap', 'includeFile' ) );

		// Set paths:
		self::$root = ROOT_PATH;
		self::$application = dirname( __FILE__ );
		self::dispatch( self::$script_controller );
	}

	/**
	 * Sets the controller and view properties and executes the controller, sending the output buffer.
	 *
	 * @param string $controller Dispatches a specific controller.
	 */
	public static function dispatch( $controller )
	{
		// Set Timezone as required by php 5.1+
		date_default_timezone_set('Europe/Madrid');

		try
		{
			$config = Config::getInstance( self::$instance );
			$domain = Domains::getInstance();

			self::$language = 'en_US';

			// This is the controller to use:
			$ctrl = self::invokeController( $controller );
			$ctrl->build();
		}
		catch ( \Exception $e )
		{
			echo ( $e->getMessage() . "\n" . $e->getTraceAsString() );
			die;
		}
	}
}
// Instance name (folder under instances):
$cwd = $_SERVER['PWD'] . '/' . $_SERVER['PHP_SELF'];
preg_match("/\/([^\/]+)\/([^\/]+)\/[^\/]+$/", $cwd, $matchs);

// Set the real and active instance name.
CLBootstrap::$instance = $matchs[1];

// Include required SIFO classes.
CLBootstrap::includeRequiredFiles();

if ( !isset( $argv[1] ) || ( '-h' == $argv[1] ) || ( '--help' == $argv[1] ) )
{
	// Dump help info:
	require_once ROOT_PATH . '/instances/common/controllers/shared/commandLine.ctrl.php';
	echo PHP_EOL . "Execute 'php $argv[0] <domain> --help' to read the help information." . PHP_EOL . PHP_EOL;
	die;
}
CLBootstrap::$command_line_params = $argv;
// Setting the domain.
FilterServer::getInstance()->setHost( $argv[1] );
