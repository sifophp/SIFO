<?php

if (is_dir(realpath(dirname(__FILE__, 5)) . '/vendor/sifophp/sifo')) {
    define('ROOT_PATH', realpath(dirname(__FILE__, 5)));
} elseif (is_dir(realpath(dirname(__FILE__, 2)) . '/src')) {
    define('ROOT_PATH', realpath(dirname(__FILE__, 2)));
} else {
    throw new \Exception('Cannot find Sifo bootstrap.');
}

require __DIR__ . '/../src/Bootstrap.php';

use Sifo\Controller\Console\AddConsoleCommands;
use Symfony\Component\Console\Application;

$application = new Application();

$console_commands = new AddConsoleCommands($application);
$console_commands->addCurrentConsoleCommands();

$application->run();
