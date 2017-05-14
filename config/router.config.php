<?php

use Sifo\Controller\Debug\DebugActionsController;
use Sifo\Controller\Debug\DebugAnalyzerController;
use Sifo\Controller\Statics\StaticsController;
use Sifo\Controller\Tools\DumpConfigFilesController;
use Sifo\Controller\Tools\I18N\FindI18NController;
use Sifo\Controller\Tools\I18N\I18NActionsController;
use Sifo\Controller\Tools\I18N\I18NRebuildController;
use Sifo\Controller\Tools\I18N\I18NSaveController;
use Sifo\Controller\Tools\I18N\I18NStatusController;
use Sifo\Controller\Tools\I18N\RebuildI18NLocalController;
use Sifo\Controller\Tools\RebuildRouterController;
use Sifo\Controller\Tools\TemplateLauncherController;

/**
 * Routes known by the application.
 *
 * The key of the array is the part of the URL that matches, and the value the controller used.
 *
 * Example:
 *
 * Given the address http://seoframework.local/blah
 *
 * $config['blah'] = 'blah/something'
 * This tells that the BlahSomethingController will be used
 *
 * The following values cannot be erased and are necessary for the proper working of the framework,
 * although you can change their values:
 *
 * __NO_ROUTE_FOUND__
 * __HOME__
 */

// Rebuild/regenerate the configuration files:
$config['rebuild'] = DumpConfigFilesController::class;

// i18n
$config['translate'] = I18NStatusController::class;
$config['translation-save'] = I18NSaveController::class;
$config['translation-rebuild'] = I18NRebuildController::class;
$config['translation-actions'] = I18NActionsController::class;

// Sifo debug
$config['sifo-debug-analyzer'] = DebugAnalyzerController::class;
$config['sifo-debug-actions'] = DebugActionsController::class;

// ____________________________________________________________________________
// Your new routes below:

// When there's something strange with your request URL, who you gonna call??
$config['__NO_ROUTE_FOUND__'] = StaticsController::class;

return $config;
