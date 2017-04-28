<?php

namespace Sifo\Controller\Tools;

use Sifo\Bootstrap;
use Sifo\Config;
use Sifo\Controller\Controller;
use Sifo\Controller\Tools\I18N\FindI18NController;
use Sifo\Exception\ConfigurationException;

/**
 * A rebuild for router when no database is used.
 *
 * Keeps the router_xx_XX files syncronized with the router_en_US or whatever you set your master file.
 */
class RebuildRouterController extends Controller
{
    const MASTER_LANGUAGE = 'en_US';

    public function build()
    {
        header('Content-Type: text/plain');

        $this->setLayout('manager/findi18n.tpl');

        try {
            $master_routes = Config::getInstance()->getConfig('lang/router_' . self::MASTER_LANGUAGE);
        } catch (ConfigurationException $e) {
            die('The master file does not exist. ' . $e->getMessage());
        }

        $findI18N = new FindI18NController();
        $files_available = $findI18N->getFilesystemFiles("instances/" . Bootstrap::$instance . "/etc/lang");

        foreach ($files_available as $key => $filename) {
            // Is a 'router' config file (but not master)
            if (strpos($filename, 'router_') !== false) {
                $translated_routes = $this->getTranslatedRoutes($filename);

                // Remove keys not present in master:
                foreach ($translated_routes as $route => $route_translated) {
                    if (!isset($master_routes[$route])) {
                        unset($translated_routes[$route]);
                        echo "Deleted route $route in $filename\n";
                    }
                }


                // Add keys not present in master
                foreach ($master_routes as $route => $route_translated) {
                    if (!isset($translated_routes[$route])) {
                        $translated_routes[$route] = $route_translated;
                    }
                }

                ksort($translated_routes);
                $this->saveConfig($filename, $translated_routes);
            }
        }

        echo "\n\nRoutes rebuild!";
        die;
    }

    protected function saveConfig($filename, $values)
    {
        $config_file = Bootstrap::$application . "/" . Bootstrap::$instance . "/etc/lang/" . $filename;
        file_put_contents($config_file, "<?php

namespace Common;\n"
            . '$config = ' . var_export($values, true) . ';');
    }

    protected function getTranslatedRoutes($filename)
    {
        $path = Bootstrap::$application . "/" . Bootstrap::$instance . "/etc/lang/" . $filename;

        include $path;
        ksort($config);

        return $config;
    }
}
