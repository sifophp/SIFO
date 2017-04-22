<?php

namespace Sifo;

/**
 * I18N. This class manages internationalization & localization.
 */
class I18N
{

    /**
     * Singleton Instance is stored here.
     *
     * @var object
     * @static
     */
    static protected $instance;

    /**
     * Stores the current active domain and language.
     *
     * @var string
     */
    static protected $active_domain_and_locale;

    /**
     * Name of the domain used to retrieve translations.
     *
     * @var string
     */
    static protected $domain;

    /**
     * Current locale defined in instance config. [ es_ES | en_US | de_DE ].
     *
     * @var string
     */
    static protected $locale;

    /**
     * Store the domain translations.
     *
     * @var array
     */
    static public $translations;

    /**
     * Store current instance translations.
     *
     * @var string
     */
    static public $current_instance = null;

    /**
     * Store instance translations.
     *
     * @var array
     */
    static public $instance_translations = null;

    /**
     * LanguageDetect class instance.
     *
     * @var object
     */
    static protected $google_translate_api_instance;

    /**
     * Private constructor, use getInstance() instead of this to get the object.
     */
    private function __construct()
    {
    }

    /**
     * Singleton instance of I18N class.
     *
     * @static
     *
     * @param string $domain The message domain (e.g: messages).
     * @param string $locale Locale used (eg.: es_ES).
     *
     * @return I18N I18N Object instance.
     */
    static public function getInstance($domain, $locale)
    {
        if (!isset(self::$instance))
        {
            self::$instance = new self();
        }

        // Establish current domain and language for further operation:
        self::setDomain($domain, $locale);

        return self::$instance;
    }

    /**
     * Set message domain.
     *
     * @param string $domain The message domain.
     */
    static public function setDomain($domain, $locale, $instance = null)
    {
        // Active domain is an indentifier in the format 'messages_es_ES' for internal use only.
        self::$active_domain_and_locale = $domain . '_' . $locale;
        self::$locale                   = $locale;
        self::$domain                   = $domain;
        self::bindTextDomain($instance);
    }

    /**
     * Support DOMAIN message catalog.
     */
    static protected function bindTextDomain($instance = null)
    {
        if (empty($instance))
        {
            $instance = Bootstrap::$instance;
        }

        // Loads all the messages into memory in case they aren't loaded before, or current instance is different than instance passed.
        if (!isset(self::$translations[self::$active_domain_and_locale]) || self::$current_instance !== $instance)
        {
            self::$current_instance = $instance;

            // Include instance messages file in case we don't have previously stored translations for this instance, domain and language.
            if (!isset(self::$instance_translations[self::$current_instance][self::$active_domain_and_locale]))
            {
                $translations_file = Config::getInstance($instance)->getConfig('locale', self::$active_domain_and_locale);
                include(ROOT_PATH . "/$translations_file");

                if (!isset($translations))
                {
                    throw new Exception_500('Failed to include a valid translations file for domain ' . self::$domain . ' and language ' . self::$locale);
                }

                self::$translations[self::$active_domain_and_locale]                                   = $translations;
                self::$instance_translations[self::$current_instance][self::$active_domain_and_locale] = $translations;
            }
            else
            {
                self::$translations[self::$active_domain_and_locale] = self::$instance_translations[self::$current_instance][self::$active_domain_and_locale];
            }
        }
    }

    /**
     * Returns the translated message.
     *
     * @param       $message string Message in source language (usually English)
     * @param array $params  If the message needs replacement of variables pass them here, in the format "%1" => $param1, "%2" => $param2
     *
     * @return <type>
     */
    static public function getTranslation($message, $params = null)
    {
        if (isset(self::$translations[self::$active_domain_and_locale][$message]) && '' != self::$translations[self::$active_domain_and_locale][$message])
        {
            $message = stripslashes(self::$translations[self::$active_domain_and_locale][$message]);
        }

        if (null !== $params && is_array($params))
        {
            foreach ($params as $key => $variable)
            {
                $message = str_replace($key, $variable, $message);
            }
        }

        return $message;
    }

    /**
     * Given a translated string, returns the original MSGID.
     *
     * @param string $message
     *
     * @return string
     */
    static public function getReverseTranslation($message)
    {
        if ($key = array_search($message, self::$translations[self::$active_domain_and_locale]))
        {
            $message = $key;
        }

        return $message;
    }

    /**
     * Returns the currently used domain.
     *
     * @return string
     */
    static public function getDomain()
    {
        return self::$domain;
    }

    /**
     * Returns the current locale (language).
     *
     * @return string
     */
    static public function getLocale()
    {
        return self::$locale;
    }
}
