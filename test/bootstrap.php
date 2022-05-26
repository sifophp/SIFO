<?php

declare(strict_types=1);

use Sifo\Bootstrap;

require 'vendor/autoload.php';

$rootDir = realpath(dir('.')->path);
if (false === defined('ROOT_PATH')) {
    define('ROOT_PATH', $rootDir);
}

$_SERVER['HTTP_HOST'] = 'sifo.local';
Bootstrap::$instance = 'example';
