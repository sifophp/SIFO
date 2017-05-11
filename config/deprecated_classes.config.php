<?php

namespace Sifo;

class_alias(Cache\Cache::class, Cache::class);
if (!class_exists(Cache::class))
{
	/** @deprecated this is an alias for Cache\Cache */
	class Cache {}
}

class_alias(Http\Client::class, Client::class);
if (!class_exists(Client::class))
{
	/** @deprecated this is an alias for Http\Client */
	class Client {}
}

class_alias(Controller\Controller::class, Controller::class);
if (!class_exists(Controller::class))
{
	/** @deprecated this is an alias for Controller\Controller */
	class Controller {}
}

class_alias(Http\Cookie::class, Cookie::class);
if (!class_exists(Cookie::class))
{
	/** @deprecated this is an alias for Http\Cookie */
	class Cookie {}
}

class_alias(Debug\Debug::class, Debug::class);
if (!class_exists(Debug::class))
{
	/** @deprecated this is an alias for Debug\Debug */
	class Debug {}
}

class_alias(Debug\Mysql::class, DebugMysql::class);
if (!class_exists(DebugMysql::class))
{
	/** @deprecated this is an alias for Debug\Mysql */
	class DebugMysql {}
}

class_alias(Container\DependencyInjector::class, DependencyInjector::class);
if (!class_exists(DependencyInjector::class))
{
	/** @deprecated this is an alias for Container\DependencyInjector */
	class DependencyInjector {}
}

class_alias(Http\Domains::class, Domains::class);
if (!class_exists(Domains::class))
{
	/** @deprecated this is an alias for Http\Domains */
	class Domains {}
}

class_alias(Exception\ConfigurationException::class, Exception_Configuration::class);
if (!class_exists(Exception_Configuration::class))
{
	/** @deprecated this is an alias for Exception\ConfigurationException */
	class Exception_Configuration {}
}

class_alias(Exception\Http\TemporalRedirect::class, Exception_302::class);
if (!class_exists(Exception_302::class))
{
	/** @deprecated this is an alias for Exception\Http\TemporalRedirect */
	class Exception_302 {}
}

class_alias(Exception\Http\PermanentRedirect::class, Exception_301::class);
if (!class_exists(Exception_301::class))
{
	/** @deprecated this is an alias for Exception\Http\PermanentRedirect */
	class Exception_301 {}
}

class_alias(Exception\Http\Unauthorized::class, Exception_401::class);
if (!class_exists(Exception_401::class))
{
	/** @deprecated this is an alias for Exception\Http\Unauthorized */
	class Exception_401 {}
}

class_alias(Exception\Http\Forbidden::class, Exception_403::class);
if (!class_exists(Exception_403::class))
{
	/** @deprecated this is an alias for Exception\Http\Forbidden */
	class Exception_403 {}
}

class_alias(Exception\Http\NotFound::class, Exception_404::class);
if (!class_exists(Exception_404::class))
{
	/** @deprecated this is an alias for Exception\Http\NotFound */
	class Exception_404 {}
}

class_alias(Exception\Http\InternalServerError::class, Exception_500::class);
if (!class_exists(Exception_500::class))
{
	/** @deprecated this is an alias for Exception\Http\InternalServerError */
	class Exception_500 {}
}

class_alias(Http\Filter\FilterCookie::class, FilterCookie::class);
if (!class_exists(FilterCookie::class))
{
	/** @deprecated this is an alias for Http\Filter\FilterCookie */
	class FilterCookie {}
}

class_alias(Http\Filter\FilterCustom::class, FilterCustom::class);
if (!class_exists(FilterCustom::class))
{
	/** @deprecated this is an alias for Http\Filter\FilterCustom */
	class FilterCustom {}
}

class_alias(Http\Filter\FilterGet::class, FilterGet::class);
if (!class_exists(FilterGet::class))
{
	/** @deprecated this is an alias for Http\Filter\FilterGet */
	class FilterGet {}
}

class_alias(Http\Filter\FilterPost::class, FilterPost::class);
if (!class_exists(FilterPost::class))
{
	/** @deprecated this is an alias for Http\Filter\FilterPost */
	class FilterPost {}
}

class_alias(Http\Filter\FilterServer::class, FilterServer::class);
if (!class_exists(FilterServer::class))
{
	/** @deprecated this is an alias for Http\Filter\FilterServer */
	class FilterServer {}
}

class_alias(Database\Model::class, Model::class);
if (!class_exists(Model::class))
{
	/** @deprecated this is an alias for Database\Model */
	class Model {}
}

class_alias(Http\Router::class, Router::class);
if (!class_exists(Router::class))
{
	/** @deprecated this is an alias for Http\Router */
	class Router {}
}

class_alias(Http\Session::class, Session::class);
if (!class_exists(Session::class))
{
	/** @deprecated this is an alias for Http\Session */
	class Session {}
}

class_alias(Database\Sphinx\Sphinxql::class, Sphinxql::class);
if (!class_exists(Sphinxql::class))
{
	/** @deprecated this is an alias for Database\Sphinx\Sphinxql */
	class Sphinxql {}
}

class_alias(Http\Urls::class, Urls::class);
if (!class_exists(Urls::class))
{
	/** @deprecated this is an alias for Http\Urls */
	class Urls {}
}
