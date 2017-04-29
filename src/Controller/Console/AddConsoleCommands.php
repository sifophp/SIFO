<?php

namespace Sifo\Controller\Console;

use Sifo\Config;
use Sifo\Exception\ConfigurationException;
use Sifo\Exception\Http\InternalServerError;
use Symfony\Component\Console\Application;

final class AddConsoleCommands
{
    /** @var Application */
    private $console_application;

    public function __construct(Application $a_console_application)
    {
        $this->console_application = $a_console_application;

        $instances_folders_pattern = ROOT_PATH . '/instances/*';
        $available_commands = [];
        foreach (glob($instances_folders_pattern) as $instance_folder)
        {
            $instance = basename($instance_folder);
            try
            {
                $available_commands = array_merge($available_commands, Config::getInstance($instance)->getConfig('commands'));
            }
            catch (ConfigurationException $e)
            {
                continue;
            }
        }

        $this->available_commands = array_map([$this, 'getClassName'], $available_commands);
    }

    public function addCurrentConsoleCommands()
    {
        foreach ($this->available_commands as $command => $full_qualified_command_path) {
            $this->console_application->add(new $full_qualified_command_path());
        }
    }

    private function getClassName($command_path)
    {
        $clean_classname = preg_replace('/.*\/([^\/]+)\/src\/Commands\/(.+)\.php/', '$1/Commands/$2', $command_path);

        $full_qualified_name = implode('\\', array_map(function ($path) {
            return ucfirst($path);
        }, explode('/', $clean_classname)));

        if (!class_exists($full_qualified_name)) {
            throw new InternalServerError('Command ' . $full_qualified_name . ' doesn\'t exists.');
        }

        return $full_qualified_name;
    }
}
