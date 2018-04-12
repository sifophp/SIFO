<?php

namespace Sifo {

    class_alias(Cache\Cache::class, Cache::class);
    if (!class_exists(Cache::class)) {
        /** @deprecated this is an alias for Cache\Cache */
        class Cache extends Cache\Cache
        {
        }
    }

    class_alias(Cache\Base::class, CacheBase::class);
    if (!class_exists(CacheBase::class)) {
        /** @deprecated this is an alias for Cache\Base */
        class CacheBase extends Cache\Base
        {
        }
    }

    class_alias(Http\Client::class, Client::class);
    if (!class_exists(Client::class)) {
        /** @deprecated this is an alias for Http\Client */
        class Client extends Http\Client
        {
        }
    }

    class_alias(Controller\Controller::class, Controller::class);
    if (!class_exists(Controller::class)) {
        /** @deprecated this is an alias for Controller\Controller */
        class Controller extends Controller\Controller
        {
        }
    }

    class_alias(Http\Cookie::class, Cookie::class);
    if (!class_exists(Cookie::class)) {
        /** @deprecated this is an alias for Http\Cookie */
        class Cookie extends Http\Cookie
        {
        }
    }

    class_alias(Debug\Debug::class, Debug::class);
    if (!class_exists(Debug::class)) {
        /** @deprecated this is an alias for Debug\Debug */
        class Debug extends Debug\Debug
        {
        }
    }

    class_alias(Debug\Mysql::class, DebugMysql::class);
    if (!class_exists(DebugMysql::class)) {
        /** @deprecated this is an alias for Debug\Mysql */
        class DebugMysql extends Debug\Mysql
        {
        }
    }

    class_alias(Container\DependencyInjector::class, DependencyInjector::class);
    if (!class_exists(DependencyInjector::class)) {
        /** @deprecated this is an alias for Container\DependencyInjector */
        class DependencyInjector extends Container\DependencyInjector
        {
        }
    }

    class_alias(Http\Domains::class, Domains::class);
    if (!class_exists(Domains::class)) {
        /** @deprecated this is an alias for Http\Domains */
        class Domains extends Http\Domains
        {
        }
    }

    class_alias(Exception\ConfigurationException::class, Exception_Configuration::class);
    if (!class_exists(Exception_Configuration::class)) {
        /** @deprecated this is an alias for Exception\ConfigurationException */
        class Exception_Configuration extends Exception\ConfigurationException
        {
        }
    }

    class_alias(Exception\Http\TemporalRedirect::class, Exception_302::class);
    if (!class_exists(Exception_302::class)) {
        /** @deprecated this is an alias for Exception\Http\TemporalRedirect */
        class Exception_302 extends Exception\Http\TemporalRedirect
        {
        }
    }

    class_alias(Exception\Http\PermanentRedirect::class, Exception_301::class);
    if (!class_exists(Exception_301::class)) {
        /** @deprecated this is an alias for Exception\Http\PermanentRedirect */
        class Exception_301 extends Exception\Http\PermanentRedirect
        {
        }
    }

    class_alias(Exception\Http\Unauthorized::class, Exception_401::class);
    if (!class_exists(Exception_401::class)) {
        /** @deprecated this is an alias for Exception\Http\Unauthorized */
        class Exception_401 extends Exception\Http\Unauthorized
        {
        }
    }

    class_alias(Exception\Http\Forbidden::class, Exception_403::class);
    if (!class_exists(Exception_403::class)) {
        /** @deprecated this is an alias for Exception\Http\Forbidden */
        class Exception_403 extends Exception\Http\Forbidden
        {
        }
    }

    class_alias(Exception\Http\NotFound::class, Exception_404::class);
    if (!class_exists(Exception_404::class)) {
        /** @deprecated this is an alias for Exception\Http\NotFound */
        class Exception_404 extends Exception\Http\NotFound
        {
        }
    }

    class_alias(Exception\Http\InternalServerError::class, Exception_500::class);
    if (!class_exists(Exception_500::class)) {
        /** @deprecated this is an alias for Exception\Http\InternalServerError */
        class Exception_500 extends Exception\Http\InternalServerError
        {
        }
    }

    class_alias(Http\Filter\Filter::class, Filter::class);
    if (!class_exists(Filter::class)) {
        /** @deprecated this is an alias for Http\Filter\Filter */
        class Filter extends Http\Filter\Filter
        {
        }
    }

    class_alias(Http\Filter\FilterCookie::class, FilterCookie::class);
    if (!class_exists(FilterCookie::class)) {
        /** @deprecated this is an alias for Http\Filter\FilterCookie */
        class FilterCookie extends Http\Filter\FilterCookie
        {
        }
    }

    class_alias(Http\Filter\FilterCustom::class, FilterCustom::class);
    if (!class_exists(FilterCustom::class)) {
        /** @deprecated this is an alias for Http\Filter\FilterCustom */
        class FilterCustom extends Http\Filter\FilterCustom
        {
        }
    }

    class_alias(Http\Filter\FilterGet::class, FilterGet::class);
    if (!class_exists(FilterGet::class)) {
        /** @deprecated this is an alias for Http\Filter\FilterGet */
        class FilterGet extends Http\Filter\FilterGet
        {
        }
    }

    class_alias(Http\Filter\FilterPost::class, FilterPost::class);
    if (!class_exists(FilterPost::class)) {
        /** @deprecated this is an alias for Http\Filter\FilterPost */
        class FilterPost extends Http\Filter\FilterPost
        {
        }
    }

    class_alias(Http\Filter\FilterServer::class, FilterServer::class);
    if (!class_exists(FilterServer::class)) {
        /** @deprecated this is an alias for Http\Filter\FilterServer */
        class FilterServer extends Http\Filter\FilterServer
        {
        }
    }

    class_alias(Database\Model::class, Model::class);
    if (!class_exists(Model::class)) {
        /** @deprecated this is an alias for Database\Model */
        class Model extends Database\Model
        {
        }
    }

    class_alias(Database\Mysql\Mysql::class, Mysql::class);
    if (!class_exists(Mysql::class)) {
        /** @deprecated this is an alias for Database\Mysql\Mysql */
        class Mysql extends Database\Mysql\Mysql
        {
        }
    }

    class_alias(Http\Router::class, Router::class);
    if (!class_exists(Router::class)) {
        /** @deprecated this is an alias for Http\Router */
        class Router extends Http\Router
        {
        }
    }

    class_alias(Http\Session::class, Session::class);
    if (!class_exists(Session::class)) {
        /** @deprecated this is an alias for Http\Session */
        class Session extends Http\Session
        {
        }
    }

    class_alias(Database\Sphinx\Sphinxql::class, Sphinxql::class);
    if (!class_exists(Sphinxql::class)) {
        /** @deprecated this is an alias for Database\Sphinx\Sphinxql */
        class Sphinxql extends Database\Sphinx\Sphinxql
        {
        }
    }

    class_alias(Http\Urls::class, Urls::class);
    if (!class_exists(Urls::class)) {
        /** @deprecated this is an alias for Http\Urls */
        class Urls extends Http\Urls
        {
        }
    }
}

namespace Common {

    use Sifo\Controller\Console\ConsoleController;

    class_alias(ConsoleController::class, SharedCommandLineController::class);
    if (!class_exists(SharedCommandLineController::class)) {
        /** @deprecated this is an alias for ConsoleController */
        abstract class SharedCommandLineController extends ConsoleController
        {
        }
    }

}
