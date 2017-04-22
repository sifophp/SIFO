<?php

namespace Sifo\Database;

use Sifo\Config;
use Sifo\Current;
use Sifo\Database;
use Sifo\Exception_500;
use Sifo\Exception_Configuration;
use Sifo\Http\Domains;

class Search
{
    /**
     * @var Current instance.
     */
    static protected $instance;

    /**
     * @var string Singleton search engine object.
     */
    static public $search_engine;

    /**
     * @var \SphinxClient Sphinx object.
     */
    protected $sphinx;

    /**
     * @var array Config used to load this connection.
     */
    protected $sphinx_config;

    /**
     * Initialize this class.
     *
     * @param $profile
     */
    protected function __construct($profile)
    {
        $this->sphinx_config = $this->getConnectionParams($profile);

        // Check if Sphinx is enabled by configuration:
        if (true === $this->sphinx_config['active'])
        {
            self::$search_engine = 'Sphinx';
            $this->sphinx        = self::connect($this->sphinx_config);
        }

        return $this->sphinx_config;
    }

    /**
     * Singleton get instance. Return one search engine object.
     *
     * @param string $profile
     *
     * @return object Config
     */
    public static function getInstance($profile = 'default')
    {
        if (!isset (self::$instance[$profile]))
        {
            if (Domains::getInstance()->getDebugMode() !== true)
            {
                self::$instance[$profile] = new Search($profile);
            }
            else
            {
                self::$instance[$profile] = new Search($profile);
            }
        }

        return self::$instance[$profile];
    }

    /**
     * Get Sphinx connection params from config files.
     *
     * @param $profile
     *
     * @throws Exception_500
     * @return array
     */
    protected function getConnectionParams($profile)
    {
        // The domains.config file has priority, let's fetch it.
        $sphinx_config = \Sifo\Http\Domains::getInstance()->getParam('sphinx');

        if (empty($sphinx_config))
        {
            try
            {
                // If the domains.config doesn't define the params, we use the sphinx.config.
                $sphinx_config = Config::getInstance()->getConfig('sphinx');

                if (isset($sphinx_config[$profile]))
                {
                    $sphinx_config = $this->checkBalancedProfile($sphinx_config[$profile]);
                }
                elseif (isset($sphinx_config['default']))
                {
                    // Is using profiles but there isn't the required one.
                    throw new \Sifo\Exception_500("Expected sphinx settings not defined for profile {$profile} in sphinx.config.");
                }
                // Deprecated:
                else
                {
                    if (Domains::getInstance()->getDebugMode() === true)
                    {
                        trigger_error(
                            "DEPRECATED: You aren't using profiles for your sphinx.config file. Please, define at least the 'default' one. (This message is only readable with the debug flag enabled)"
                        );
                    }
                }
                $sphinx_config['config_file'] = 'sphinx';
            }
            catch (Exception_Configuration $e)
            {
                throw new \Sifo\Exception_500('You must define the connection params in sphinx.config or domains.config file');
            }
        }
        else
        {
            $sphinx_config['config_file'] = 'domains';
        }

        return $sphinx_config;
    }

    /**
     * Check if one profile has balanced servers or single server. Returns the connection to use.
     *
     * @param $sphinx_config
     *
     * @return array
     */
    private function checkBalancedProfile($sphinx_config)
    {
        if (isset($sphinx_config[0]) && is_array($sphinx_config[0]))
        {
            $lb = new Database\LoadBalancerSearch();
            $lb->setNodes($sphinx_config);
            $selected_server = $lb->get();
            $sphinx_config   = $sphinx_config[$selected_server];
        }

        return $sphinx_config;
    }

    /**
     * Delegate all calls to the proper class.
     *
     * @param string $method
     * @param mixed  $args
     *
     * @return mixed
     */
    function __call($method, $args)
    {
        if (is_object($this->sphinx))
        {
            return call_user_func_array(array($this->sphinx, $method), $args);
        }

        return null;
    }

    /**
     * Use this method to connect to Sphinx.
     *
     * @param $node_properties
     *
     * @return \SphinxClient
     * @throws Exception_500
     */
    static function connect($node_properties)
    {
        if (true === $node_properties['active'])
        {
            $sphinx = new \SphinxClient();
            $sphinx->SetServer($node_properties['server'], $node_properties['port']);

            // If it's defined a time out connection in config file:
            if (isset($node_properties['time_out']))
            {
                $sphinx->SetConnectTimeout($node_properties['time_out']);
            }

            // Check if Sphinx is listening:
            if (false === $sphinx->Status())
            {
                throw new \Sifo\Exception_500('Sphinx (' . $node_properties['server'] . ':' . $node_properties['port'] . ') is down!');
            }
        }

        return $sphinx;
    }
}
