<?php

namespace Sifo;

use Sifo\Controller\Debug\CommandLineDebugController;
use Sifo\Http\Domains;
use Symfony\Component\HttpFoundation\Request;

require_once ROOT_PATH . '/vendor/sifophp/sifo/src/Bootstrap.php';

class CLBootstrap extends Bootstrap
{
    private static $command_line_params;
    private static $script_controller;
    private static $command_list_hostname;

    public static function executeConsoleScript(string $console_controller)
    {
        self::$script_controller = $console_controller;
        self::$command_list_hostname = (!empty($_SERVER['argv'][1])) ? $_SERVER['argv'][1] : null;
        self::$command_line_params = (!empty($_SERVER['argv'])) ? $_SERVER['argv'] : null;
        $_SERVER['HTTP_HOST'] = self::$command_list_hostname;
        $request = Request::createFromGlobals();

        self::execute($request);
    }

    /**
     * Sets the controller and view properties and executes the controller, sending the output buffer.
     *
     * @param string $controller_path Dispatches a specific controller. Defaults to null for compatibility with Bootstrap::dispatch
     */
    public static function dispatch(string $controller = null)
    {
        // Set Timezone as required by php 5.1+
        date_default_timezone_set('Europe/Madrid');

        if (null === $controller) {
            $controller = self::$script_controller;
        }

        try {
            self::$language = 'en_US';

            // This is the controller to use:
            $ctrl = self::invokeController($controller);
            self::$controller = $controller;
            $ctrl->build();

            // Debug:
            if (self::$domain->getDebugMode()) {
                $ctrl_debug = self::invokeController(CommandLineDebugController::class);
                $ctrl_debug->build();
            }
        } catch (\Exception $e) {
            echo('[' . get_class($e) . '] ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            die;
        }
    }

    public static function getCommandLineParams()
    {
        return self::$command_line_params;
    }
}

// Disable whatever buffering default config.
@ob_end_flush();

// Instance name (folder under instances):
$cwd = $_SERVER['PWD'] . '/' . $_SERVER['PHP_SELF'];
preg_match("/\/([^\/]+)\/([^\/]+)\/[^\/]+$/", $cwd, $matchs);

// Set the real and active instance name.
$auto_detected_instance = $matchs[1];

if (extension_loaded('newrelic') && !empty($auto_detected_instance)) {
    newrelic_set_appname(ucfirst($auto_detected_instance));
}

if (!isset($argv[1]) || ('-h' == $argv[1]) || ('--help' == $argv[1])) {
    echo PHP_EOL . "Execute 'php $argv[0] <domain> --help' to read the help information." . PHP_EOL . PHP_EOL;
    echo "Your available domains:" . PHP_EOL;
    $available_domains = Domains::getAvailableDomainsForInstance($auto_detected_instance);
    echo implode($available_domains, PHP_EOL);
    die;
}

