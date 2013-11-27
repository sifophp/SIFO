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
	static $controller = null ;

	/**
	 * Starts the execution. Root path is passed to avoid recalculation.
	 *
	 * @param null $instance_name Name of the instance. Required for Bootsrap::execute compatibility.
	 * @param null $controller_name Script that will be executed. Required for Bootsrap::execute compatibility.
	 */
	public static function execute( $instance_name = null, $controller_name = null )
	{
		if ( !isset( $controller_name ) )
		{
			$controller_name = self::$script_controller;
		}

		// Register autoloader:
		spl_autoload_register( array( '\\Sifo\Bootstrap', 'includeFile' ) );

		// Set paths:
		self::$root = ROOT_PATH;
		self::$application = dirname( __FILE__ );

		Benchmark::getInstance()->timingStart();
		self::dispatch( $controller_name );
		Benchmark::getInstance()->timingStop();
	}

	/**
	 * Sets the controller and view properties and executes the controller, sending the output buffer.
	 *
	 * @param string $controller Dispatches a specific controller. Defaults to null for compatibility with Bootstrap::dispatch
	 */
	public static function dispatch( $controller = null )
	{
		// Set Timezone as required by php 5.1+
		date_default_timezone_set('Europe/Madrid');

		try
		{

			self::$language = 'en_US';

			// This is the controller to use:
			$ctrl = self::invokeController( $controller );
			self::$controller = $controller;
			$ctrl->build();

			// Debug:
			if ( Domains::getInstance()->getDebugMode() )
			{
				$ctrl_debug = self::invokeController( 'DebugCommandLineDebug' );
				$ctrl_debug->build();
			}
		}
		catch ( \Exception $e )
		{
			echo ( $e->getMessage() . "\n" . $e->getTraceAsString() );
			die;
		}
	}

	public static function is_domain( $var )
	{
		return ( false !== strpos( $var, "." ) );
	}

	public static function get_available_domains()
	{
		$domain_configuration = Config::getInstance()->getConfig( 'domains' );
		$configuration_keys = array_keys( $domain_configuration );
		$available_domains = array_filter( $configuration_keys, "self::is_domain");

		return $available_domains;
	}

}

// Disable whatever buffering default config.
@ob_end_flush();

// Instance name (folder under instances):
$cwd = $_SERVER['PWD'] . '/' . $_SERVER['PHP_SELF'];
preg_match("/\/([^\/]+)\/([^\/]+)\/[^\/]+$/", $cwd, $matchs);

// Set the real and active instance name.
CLBootstrap::$instance = $matchs[1];

if ( extension_loaded( 'newrelic' ) && isset( CLBootstrap::$instance ) )
{
	newrelic_set_appname( ucfirst( CLBootstrap::$instance ) );
}

// Include required SIFO classes.
CLBootstrap::includeRequiredFiles();

if ( !isset( $argv[1] ) || ( '-h' == $argv[1] ) || ( '--help' == $argv[1] ) )
{
	// Dump help info:
	require_once ROOT_PATH . '/instances/common/controllers/shared/commandLine.ctrl.php';
	echo PHP_EOL . "Execute 'php $argv[0] <domain> --help' to read the help information." . PHP_EOL . PHP_EOL;
	echo "Your available domains:" . PHP_EOL;
	$available_domains = CLBootstrap::get_available_domains();
	echo implode( $available_domains, PHP_EOL );
	die;
}
CLBootstrap::$command_line_params = $argv;

// Setting the domain.
FilterServer::getInstance()->setHost( $argv[1] );
