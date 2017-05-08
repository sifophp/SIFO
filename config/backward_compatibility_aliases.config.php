<?php

$config['Sifo\Cache'] = \Sifo\Cache\Cache::class;
$config['Sifo\Client'] = \Sifo\Http\Client::class;
$config['Sifo\Controller'] = \Sifo\Controller\Controller::class;
$config['Sifo\Cookie'] = \Sifo\Http\Cookie::class;
$config['Sifo\Debug'] = \Sifo\Debug\Debug::class;
$config['Sifo\DebugMysql'] = \Sifo\Debug\Mysql::class;
$config['Sifo\DependencyInjector'] = \Sifo\Container\DependencyInjector::class;
$config['Sifo\Domains'] = \Sifo\Http\Domains::class;
$config['Sifo\Exception_Configuration'] = \Sifo\Exception\ConfigurationException::class;
$config['Sifo\Exception_302'] = \Sifo\Exception\Http\TemporalRedirect::class;
$config['Sifo\Exception_301'] = \Sifo\Exception\Http\PermanentRedirect::class;
$config['Sifo\Exception_401'] = \Sifo\Exception\Http\Unauthorized::class;
$config['Sifo\Exception_403'] = \Sifo\Exception\Http\Forbidden::class;
$config['Sifo\Exception_404'] = \Sifo\Exception\Http\NotFound::class;
$config['Sifo\Exception_500'] = \Sifo\Exception\Http\InternalServerError::class;
$config['Sifo\FilterCookie'] = \Sifo\Http\Filter\FilterCookie::class;
$config['Sifo\FilterCustom'] = \Sifo\Http\Filter\FilterCustom::class;
$config['Sifo\FilterGet'] = \Sifo\Http\Filter\FilterGet::class;
$config['Sifo\FilterPost'] = \Sifo\Http\Filter\FilterPost::class;
$config['Sifo\FilterServer'] = \Sifo\Http\Filter\FilterServer::class;
$config['Sifo\Model'] = \Sifo\Database\Model::class;
$config['Sifo\Router'] = \Sifo\Http\Router::class;
$config['Sifo\Session'] = \Sifo\Http\Session::class;
$config['Sifo\Sphinxql'] = \Sifo\Database\Sphinx\Sphinxql::class;
$config['Sifo\Urls'] = \Sifo\Http\Urls::class;
