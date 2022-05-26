<?php
/**
 *
 * Class CLBootstrap
 */
namespace Sifo;

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
	public static function execute( $instance_name = null, $controller_name = null, $psr_container = null )
	{
		if ( !isset( $controller_name ) )
		{
			$controller_name = self::$script_controller;
		}


        self::$container = DependencyInjector::getInstance(null, $psr_container);

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
	public static function dispatch( $controller = null, $container = null )
	{
		// Set Timezone as required by php 5.1+
		date_default_timezone_set('Europe/Madrid');

        self::$language = 'en_US';

        // This is the controller to use:
        $ctrl = self::invokeController( $controller, $container );
        $ctrl->setContainer(static::$container);
        self::$controller = $controller;
        $ctrl->build();

        // Debug:
        if ( Domains::getInstance()->getDebugMode() )
        {
            $ctrl_debug = self::invokeController( 'DebugCommandLineDebug' );
            $ctrl_debug->build();
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
preg_match("/\/([^\/]+)\/([^\/]+)\/[^\/]+$/", $_SERVER['PHP_SELF'], $matchs);

// Set the real and active instance name.
CLBootstrap::$instance = $matchs[1];

if ( extension_loaded( 'newrelic' ) && isset( CLBootstrap::$instance ) )
{
	newrelic_set_appname( ucfirst( CLBootstrap::$instance ) );
}

if (false === isset($argv)) {
    $argv = $_SERVER['argv'] ?? [];
}

if ( !isset( $argv[1] ) || ( '-h' == $argv[1] ) || ( '--help' == $argv[1] ) )
{
	// Dump help info:
	require_once ROOT_PATH . '/vendor/sifophp/sifo-common-instance/controllers/shared/commandLine.php';

	echo PHP_EOL . "Execute 'php $argv[0] <domain> --help' to read the help information." . PHP_EOL . PHP_EOL;
	echo "Your available domains:" . PHP_EOL;
	$available_domains = CLBootstrap::get_available_domains();
	echo implode(PHP_EOL, $available_domains);
	die;
}
CLBootstrap::$command_line_params = $argv;

// Setting the domain.
FilterServer::getInstance()->setHost( $argv[1] );
