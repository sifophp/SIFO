<?php

namespace Sifo\Http;

use Sifo\Bootstrap;
use Sifo\Config;
use Sifo\Exception\Http\InternalServerError;
use Sifo\Exception\Http\NotFound;
use Sifo\Exception\Http\PermanentRedirect;
use Sifo\Exception\UnknownDomainException;
use Sifo\Http\Filter\FilterServer;

class Domains
{
    private static $available_configurations;
    private static $singleton;
    private $domain;
    private $language;
    private $language_subdomain = false;
    private $language_domain = false;
    private $subdomain = false;
    private $static_host = false;
    private $media_host = false;
    private $dev_mode = false;
    private $has_debug = false;
    private $instance;
    private $domain_configuration = [];
    private $php_inis = false;
    private $auth_data = [];
    private $http_host;
    private $port;
    private $instance_inheritance;
    public $www_mode = false;
    public $valid_domain = true;

    private function populateAvailableDomains()
    {
        if (null !== self::$available_configurations) {
            return self::$available_configurations;
        }

        $instances_folders = array_map(function ($folder) {
            return basename($folder);
        }, array_filter(glob(ROOT_PATH . '/instances/*', GLOB_ONLYDIR), function ($dir) {
            return file_exists($dir . '/config/domains.config.php');
        }));

        $available_configurations = [];
        foreach ($instances_folders as $instance) {
            $available_configurations[$instance] = Config::getInstance($instance)->getConfig('domains');
        }

        self::$available_configurations = $available_configurations;
    }

    public static function getInstance(string $hostname = null): Domains
    {
        if (null === $hostname) {
            $hostname = Bootstrap::$request->getHost();
        }

        if (!isset(self::$singleton[$hostname])) {
            self::$singleton[$hostname] = new Domains($hostname);
        }

        return self::$singleton[$hostname];
    }

    private function checkDomainRedirections()
    {
        array_walk(self::$available_configurations, function ($configuration) {
            if (empty($configuration['redirections'])) {
                return;
            }
            array_walk($configuration['redirections'], function ($redirect) {
                if ($this->http_host == $redirect['from']) {
                    throw new PermanentRedirect($redirect['to']);
                }
            });
        });
    }

    private function identifyInstanceAndSetConfiguration()
    {
        foreach (self::$available_configurations as $instance => $configuration) {
            $available_domains = array_keys($configuration);
            foreach ($available_domains as $app_domain) {
                if (strstr($this->http_host, $app_domain)) {
                    $this->instance = $instance;
                    $this->domain = $app_domain;
                    $this->setInstanceInheritanceBasedOnConfiguration($configuration);
                    $this->setLanguageBasedOnConfiguration($configuration[$app_domain]);
                    $this->setSubdomainRelatedKeys($app_domain, $configuration[$app_domain]);
                    $this->setWwwMode($configuration[$app_domain]);
                    $this->dev_mode = ($configuration[$app_domain]['devel'] === true);
                    $this->has_debug = !empty($settings['has_debug']) ? (bool)$settings['has_debug'] : $this->dev_mode;
                    $this->setAuthentication($configuration[$app_domain]);
                    $this->setStaticAndMediaHosts($configuration[$app_domain]);
                    $this->setPhpIni($configuration[$app_domain]);
                    $this->domain_configuration = $configuration[$app_domain];
                    return;
                }
            }
        }

        throw new UnknownDomainException('Unknown domain: ' . $this->http_host);
    }

    private function setInstanceInheritanceBasedOnConfiguration(array $configuration)
    {
        if (isset($configuration['instance_inheritance'])) {
            array_push($configuration['instance_inheritance'], $this->instance);
            $this->instance_inheritance = $configuration['instance_inheritance'];
            return;
        }
        $this->instance_inheritance = [$this->instance];
    }

    private function setLanguageBasedOnConfiguration(array $configuration)
    {
        if (!isset($configuration['language']) || !isset($configuration['language_domain'])) {
            throw new InternalServerError('The language MUST be declared in domains.config file');
        }

        $this->language = $configuration['language'];
        $this->language_domain = $configuration['language_domain'];
    }


    private function setSubdomainRelatedKeys(string $app_domain, array $configuration)
    {
        if ($app_domain == $this->http_host) {
            return;
        }
        $this->subdomain = str_replace('.' . $app_domain, '', $this->http_host);

        if (isset($configuration['lang_in_subdomain']) && is_array($configuration['lang_in_subdomain'])) {
            $subdomain_pieces = explode('.', $this->subdomain);
            $language = array_pop($subdomain_pieces);
            if (!isset($configuration['lang_in_subdomain'][$language])) {
                throw new NotFound('Unkonwn domain in subdomain: ' . $this->subdomain);
            }
            $this->language = $configuration['lang_in_subdomain'][$language];
            $this->language_subdomain = $this->subdomain;
        }
    }

    private function setWwwMode(array $configuration)
    {
        $this->www_mode = isset($configuration['www_as_subdomain']) && true === $configuration['www_as_subdomain'];
    }

    private function setAuthentication(array $configuration)
    {
        if (empty($configuration['auth'])) {
            return;
        }
        $auth_parts = explode(',', $configuration['auth']);
        $this->auth_data['user'] = $auth_parts[0];
        $this->auth_data['password'] = $auth_parts[1];
        $this->auth_data['hash'] = sha1(date('hdmY') . $configuration['auth']);
    }

    private function setStaticAndMediaHosts(array $configuration)
    {
        if (!empty($configuration['static_host'])) {
            $this->static_host = $configuration['static_host'];
        }

        if (!empty($configuration['media_host'])) {
            $this->media_host = $configuration['media_host'];
        }
    }

    private function setPhpIni(array $configuration)
    {
        if (!empty($configuration['php_ini_sets'])) {
        $this->php_inis = $configuration['php_ini_sets'];
    }
    }

    private function __construct(string $hostname)
    {
        $this->http_host = $hostname;
        $this->populateAvailableDomains();
        $this->checkDomainRedirections();
        $this->identifyInstanceAndSetConfiguration();

    }

    /**
     * Changes Domain data in execution time.
     *
     * @param string $hostname
     */
    public function changeDomain(string $hostname)
    {
        $this->__construct($hostname);
    }

    public function getAuthData()
    {
        return $this->auth_data;
    }

    public function getPhpInis()
    {
        return $this->php_inis;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function getSubdomain()
    {
        return $this->subdomain;
    }

    public function getDevMode(): bool
    {
        return $this->dev_mode;
    }

    /**
     * Returns if debug is enabled or not.
     *
     * @return bool
     */
    public function getDebugMode(): bool
    {
        return $this->has_debug;
    }

    /**
     * Change debug mode during the execution.
     *
     * @param $mode
     */
    public function setDebugMode(bool $mode)
    {
        $this->has_debug = $mode;
    }

    /**
     * Allows to retrieve any parameter declared in domains.config by Key.
     *
     * @param string $param_name
     *
     * @return mixed
     */
    public function getParam($param_name)
    {
        if (isset($this->domain_configuration[$param_name])) {
            return $this->domain_configuration[$param_name];
        }

        return false;
    }

    /**
     * Returns database parameters (relational, such as Mysql).
     *
     * @return array
     */
    public function getDatabaseParams()
    {
        return $this->getParam('database');
    }

    public function getExternalParams()
    {
        return $this->getParam('external');
    }

    /**
     * Returns the language set in the domain or false if this configuration is disabled by config.
     *
     * @return string|false
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Returns the subdomain used to load this language.
     *
     * @return string|false
     */
    public function getLanguageSubdomain()
    {
        return $this->language_subdomain;
    }

    /**
     * Returns the domain used in this language.
     *
     * @return string|false
     */
    public function getLanguageDomain()
    {
        return $this->language_domain;
    }

    /**
     * Returns the hosts holding the static content (images, css, js...)
     *
     * @return string
     */
    public function getStaticHost()
    {
        $filter_server = FilterServer::getInstance();

        if ($filter_server->getString('HTTPS') == 'on' || $filter_server->getString('HTTP_X_FORWARDED_PROTO') == 'https') {
            return str_replace('http://', 'https://', $this->static_host);
        }

        return $this->static_host;
    }

    /**
     * Returns the hosts holding the multimedia content (user avatars, videos, audio, photos...)
     *
     * @return string
     */
    public function getMediaHost()
    {
        $filter_server = FilterServer::getInstance();

        if ($filter_server->getString('HTTPS') == 'on' || $filter_server->getString('HTTP_X_FORWARDED_PROTO') == 'https') {
            return str_replace('http://', 'https://', $this->media_host);
        }

        return $this->media_host;
    }

    /**
     * Return the instance inheritance.
     *
     * @return array
     */
    public function getInstanceInheritance()
    {
        return $this->instance_inheritance;
    }

    public function getInstanceName()
    {
        return $this->instance;
    }
}
