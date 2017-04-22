<?php

namespace Sifo\Database;

use Sifo\Domains;
use Sifo\I18N;
use Sifo\Registry;
use Sifo\unknown;

class Model extends Database
{
    public function __construct()
    {
        $this->init();

        return Database::getInstance();
    }

    /**
     * Use this method as constructor in chidren.
     *
     * @return unknown
     */
    protected function init()
    {
        return true;
    }

    /**
     * Returns an element in the registry.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function inRegistry($key)
    {
        $reg = Registry::getInstance();
        if ($reg->keyExists($key))
        {
            return $reg->get($key);
        }

        return false;
    }

    /**
     * Stores in the registry a value with the given key.
     *
     * @param string $key
     * @param mixed  $value
     */
    protected function storeInRegistry($key, $value)
    {
        $reg = Registry::getInstance()->set($key, $value);
    }

    /**
     * Returns the translation of a string
     *
     * @param string $subject
     * @param string $var_1
     * @param string $var2
     * @param string $var_n
     *
     * @return string
     */
    public function translate($subject, $var_1 = '', $var2 = '', $var_n = '')
    {
        $args      = func_get_args();
        $variables = array();
        if (1 < count($args))
        {
            foreach ($args as $key => $value)
            {
                $variables['%' . $key] = $value;
            }
        }

        unset($variables['%0']);

        return I18N::getInstance('messages', Domains::getInstance()->getLanguage())->getTranslation($subject, $variables);
    }
}
