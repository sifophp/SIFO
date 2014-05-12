<?php

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
// ____________________________________________________________________________
// When the router doesn't know what to do with the URL, who is going to handle it?:
$config['__NO_ROUTE_FOUND__'] = 'static/index';

// Controller used when no path is passed (home page).
$config['__HOME__'] = 'home/index';

// Rebuild/regenerate the configuration files:
$config['rebuild'] = 'manager/rebuild';
$config['findi18n'] = 'manager/findi18n';
$config['rebuild-i18n-local'] = 'manager/rebuildi18nLocal';
$config['rebuild-router'] = 'manager/rebuildRouter';

// i18n
$config['translate'] = 'i18n/status';
$config['translation-save'] = 'i18n/save';
$config['translation-rebuild'] = 'i18n/rebuild';
$config['translation-actions'] = 'i18n/actions';

// Sifo debug
$config['sifo-debug-analyzer'] = 'debug/analyzer';
$config['sifo-debug-actions'] = 'debug/actions';

// Used in debug mode Mail Interception.
$config['mail-continue'] = 'debug/mail';

// Template simulator
$config['template-launcher'] = 'manager/templateLauncher';

// ____________________________________________________________________________
// Your new routes below:
$config["locales"]			= 'locales/index';
$config["locales-save"]		= 'locales/save';
