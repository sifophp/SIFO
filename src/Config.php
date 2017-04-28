<?php

namespace Sifo;

use Sifo\Exception\ConfigurationException;

class Config
{

    /**
     * Defines what profile do you want to use to load libraries. This must be a key in domains.config.php
     *
     * @var string
     */
    static public $libraries_profile = 'default';

    /**
     * Singleton instance.
     *
     * @var self
     */
    static protected $instance;

    /**
     * File used as base to load the paths to configuration files.
     *
     * @var string
     */
    protected $configuration_files_path = 'configuration_files.config.php';

    /**
     * Variables taken from the config are stored here in the class context.
     *
     * @var array
     */
    protected $config_values;

    /**
     * Content inside the $configuration_files file specifying in which instance is found every config.
     *
     * @var array
     */
    protected $paths_to_configs = [];

    protected function __construct($instance_name)
    {
        if ($instance_name === 'tests') {
            $config_path = ROOT_PATH . '/' . $instance_name . "/config/";
        } else {
            $config_path = ROOT_PATH . "/instances/" . $instance_name . "/config/";
        }

        include($config_path . $this->configuration_files_path);
        $this->paths_to_configs = $config;
    }

    /**
     * Singleton of config class.
     *
     * @param string $instance_name Instance Name, needed to determine correct paths.
     *
     * @return Config
     */
    public static function getInstance($instance_name = null)
    {
        if (!isset($instance_name)) {
            $instance_name = Bootstrap::$instance;
        }

        if (!isset (self::$instance[$instance_name])) {
            self::$instance[$instance_name] = new self($instance_name);
        }

        return self::$instance[$instance_name];
    }

    /**
     * Loads the desired config file for a given valid 'profile'.
     *
     * @param string $profile The requested profile.
     *
     * @throws ConfigurationException When isn't set the self::PROFILE_NAME_FOR_CONFIG_FILES section or the desired profile.
     * @return boolean
     */
    protected function loadConfig($profile)
    {
        if (!isset($this->paths_to_configs[$profile])) {
            throw new ConfigurationException("The profile '$profile' was not found");
        }

        if (!include(ROOT_PATH . '/' . $this->paths_to_configs[$profile])) {
            throw new ConfigurationException("Failed to include file " . ROOT_PATH . '/' . $this->paths_to_configs[$profile]);
        }

        // The file was correctly included. We include the variable $config found.
        if (!isset($config)) {
            throw new ConfigurationException('The configuration files must have a variable named $config');
        }

        return $config;
    }

    /**
     * Gets the profile config variables for the desired profile.
     *
     * @param string $profile The requested profile.
     * @param string $group The requested group inside the profile.
     *
     * @throws ConfigurationException When the selected group or profile doesn't exist.
     * @return mixed $config_values The config values in the config file of the current profile.
     */
    public function getConfig($profile, $group = null)
    {
        if (!isset($this->config_values[$profile])) {
            $this->config_values[$profile] = $this->loadConfig($profile);
        }

        if (is_null($group)) {
            return $this->config_values[$profile];
        }

        if (isset($this->config_values[$profile][$group])) {
            return $this->config_values[$profile][$group];
        }

        throw new ConfigurationException("The group '$group' for profile '$profile' was never set.", E_USER_ERROR);
    }
}

