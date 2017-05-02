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
 * Given the address http://list.seoframework.local/blah
 * $config['list'] = 'list/index';
 * This tells that the subdomain list uses the ListIndexController
 *
 * $config['blah'] = 'blah/something'
 * This tells that the BlahSomethingController is used instead.
 *
 * If a subdomain matches a routing is used instead of any other part of the URL. With the given
 * example if the two routes were given, only the list/index would apply.
 *
 * The following values cannot be erased and are necessary for the proper working of the  framework,
 * although you can change their values:
 *
 * __NO_ROUTE_FOUND__
 * __HOME__
 * rebuild
 */

// Rebuild/regenerate the configuration files:
$config['rebuild'] = DumpConfigFilesController::class;
$config['findi18n'] = FindI18NController::class;
$config['rebuild-i18n-local'] = RebuildI18NLocalController::class;
$config['rebuild-router'] = RebuildRouterController::class;

// i18n
$config['translate'] = I18NStatusController::class;
$config['translation-save'] = I18NSaveController::class;
$config['translation-rebuild'] = I18NRebuildController::class;
$config['translation-actions'] = I18NActionsController::class;

// Sifo debug
$config['sifo-debug-analyzer'] = DebugAnalyzerController::class;
$config['sifo-debug-actions'] = DebugActionsController::class;

// Template simulator
$config['template-launcher'] = TemplateLauncherController::class;

// ____________________________________________________________________________
// Your new routes below:

// When there's something strange with your request URL, who yo gonna call??
$config['__NO_ROUTE_FOUND__'] = StaticsController::class;

