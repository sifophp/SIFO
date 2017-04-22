<?php

namespace Sifo;

class Domains
{
    protected $domain;
    protected $language = false;
    protected $language_subdomain = false;
    protected $language_domain = false;
    protected $subdomain = false;
    protected $static_host = false;
    protected $media_host = false;
    protected $dev_mode = false;
    protected $has_debug = false;
    protected $instance;
    protected $domain_configuration = array();
    protected $php_inis = false;
    protected $redirect;
    protected $auth_data = array();
    protected $http_host;
    protected $port;
    protected $core_inheritance;
    protected $instance_inheritance;
    public $www_mode = false;
    public $valid_domain = true;
    static private $singleton;


    /**
     * Singleton for domain calculation.
     *
     * @return Domains
     */
    static public function getInstance()
    {
        if (!isset(self::$singleton))
        {
            self::$singleton = new Domains();
        }

        return self::$singleton;
    }

    private function __construct($host = null)
    {
        $filter_server = FilterServer::getInstance();

        // If the host is defined by user we use it.
        $this->http_host = $host;

        // In other case we use the server host.
        if (null === $host)
        {
            $host_data       = explode(':', $filter_server->getString("HTTP_HOST")); // Explode hostname and port.
            $this->http_host = $host_data[0];
            $this->port      = isset($host_data[1]) ? $host_data[1] : null;
        }

        $this->domain_configuration = Config::getInstance()->getConfig('domains');

        if (isset($this->domain_configuration['instance_type']))
        {
            unset($this->domain_configuration['instance_type']);
        }

        if (isset($this->domain_configuration['core_inheritance']))
        {
            $this->core_inheritance = $this->domain_configuration['core_inheritance'];
            unset($this->domain_configuration['core_inheritance']);
        }
        else
        {
            $this->core_inheritance = array('Sifo');
        }

        // Get the domain inheritance.
        if (isset($this->domain_configuration['instance_inheritance']))
        {
            $this->instance_inheritance = $this->domain_configuration['instance_inheritance'];
            unset($this->domain_configuration['instance_inheritance']);
        }
        else
        {
            $this->instance_inheritance = array('common');
        }

        if (isset($this->domain_configuration['redirections']) && is_array($this->domain_configuration['redirections']))
        {

            foreach ($this->domain_configuration['redirections'] as $redirection)
            {
                if (strtolower($this->http_host == $redirection['from']))
                {
                    $this->redirect = $redirection['to'];
                    $this->redirect .= isset($this->port) ? ':' . $this->port : null;
                    $this->redirect .= $filter_server->getString('REQUEST_URI');
                }
            }

            // If you add more non-domain entries in the config file, must be unset here.
            unset($this->domain_configuration['redirections']);
        }

        // Iterates over all known domains and sets the first match as the current domain.
        foreach ($this->domain_configuration as $host => $settings)
        {
            if (isset($settings['libraries_profile']))
            {
                Config::$libraries_profile = $settings['libraries_profile'];
            }
            // Domain configuration forces language.
            if (isset($settings['language']) && isset($settings['language_domain']))
            {
                $this->setLanguage($settings['language']);
                $this->language_domain = $settings['language_domain'];
            }
            else
            {
                throw new Exception_500('The language MUST be declared in domains.config file');
            }

            if (false !== strstr(strtolower($this->http_host), $host))
            {
                $subdomain = str_replace('.' . $host, '', $this->http_host);
                if ($subdomain != $host)
                {
                    // The language is stated in the domain.
                    if (isset($settings['lang_in_subdomain']) && false != $settings['lang_in_subdomain'])
                    {
                        $subdomain_pieces = explode('.', $subdomain);
                        $language         = array_pop($subdomain_pieces);
                        // Check if the language is known by the configuration:
                        if (isset($settings['lang_in_subdomain'][$language]))
                        {
                            $this->setLanguage($settings['lang_in_subdomain'][$language]);
                        }
                        else
                        {
                            // Language by default:
                            $this->setLanguage($settings['language']);
                            $this->valid_domain = false;

                            // The subdomain given is unknown. Apache shouldn't let the application arrive at this point.
                            // throw new Exception_404( "Unknown language subdomain $language in domain $host" );
                        }

                        $subdomain = implode('.', $subdomain_pieces);
                    }
                    $this->subdomain = $subdomain;
                }

                if (isset($settings['www_as_subdomain']) && true === $settings['www_as_subdomain'])
                {
                    $this->www_mode = true;
                }
                else
                {
                    $this->www_mode = false;
                }

                $this->domain    = $host;
                $this->dev_mode  = ($settings['devel'] === true);
                $this->has_debug = isset($settings['has_debug']) ? $settings['has_debug'] === true : $this->dev_mode;

                // See if the domain changes the instance used, otherwise 'default' is assumed.
                if (isset($settings['instance']) && !empty($settings['instance']))
                {
                    $this->instance = $settings['instance'];
                    // Add the current instance to the inheritance:
                    $this->instance_inheritance[] = $settings['instance'];
                }

                // Domain requires auth:
                if (isset($settings['auth']) && $settings['auth'] != false)
                {
                    $auth_parts                  = explode(',', $settings['auth']);
                    $this->auth_data['user']     = $auth_parts[0];
                    $this->auth_data['password'] = $auth_parts[1];
                    $this->auth_data['hash']     = sha1(date('hdmY') . $settings['auth']);
                }

                if (isset($settings['static_host']))
                {
                    $this->static_host = $settings['static_host'];
                }

                if (isset($settings['media_host']))
                {
                    $this->media_host = $settings['media_host'];
                }

                if (isset($settings['lang_in_subdomain']) && is_array($settings['lang_in_subdomain']))
                {
                    foreach ($settings['lang_in_subdomain'] as $subdomain => $lang)
                    {
                        if ($this->language == $lang)
                        {
                            $this->language_subdomain = $subdomain;
                            break;
                        }
                    }
                }

                if ((isset($settings['php_ini_sets']) && !empty($settings['php_ini_sets'])))
                {
                    $this->php_inis = $settings['php_ini_sets'];
                }

                break;
            }
        }

        // If a domain is not configured, we launch a 404 error.
        if (!isset($this->instance) && !isset($this->redirect))
        {
            throw new DomainsException('Unknown domain.');
        }
    }

    /**
     * Changes Domain data in execution time.
     *
     * @param $domain string Domain which you want to load.
     */
    public function changeDomain($domain)
    {
        $this->__construct($domain);
    }

    public function getAuthData()
    {
        return $this->auth_data;
    }

    public function getRedirect()
    {
        return $this->redirect;
    }

    public function getPHPInis()
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

    public function getDevMode()
    {
        return $this->dev_mode;
    }

    /**
     * Returns if debug is enabled or not.
     *
     * @return bool
     */
    public function getDebugMode()
    {
        return $this->has_debug;
    }

    /**
     * Change debug mode during the execution.
     *
     * @return bool
     */
    public function setDebugMode($mode)
    {
        $this->has_debug = (bool) $mode;
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
        if (isset($this->domain_configuration[$this->getDomain()][$param_name]))
        {
            return $this->domain_configuration[$this->getDomain()][$param_name];
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

    public function setLanguage($lang)
    {
        $this->language = $lang;
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

        // Fix for hostings where the HTTPS server value is not empty but "off" (like Webfaction).
        if ($filter_server->getString('HTTPS') == 'on' || $filter_server->getString('HTTP_X_FORWARDED_PROTO') == 'https')
        {
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

        if ($filter_server->getString('HTTPS') == 'on' || $filter_server->getString('HTTP_X_FORWARDED_PROTO') == 'https')
        {
            return str_replace('http://', 'https://', $this->media_host);
        }

        return $this->media_host;
    }

    /**
     * Return the core inheritance. Used for active new versions.
     *
     * @return array
     */
    public function getCoreInheritance()
    {
        return $this->core_inheritance;
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
}

class DomainsException extends \Exception
{
}
