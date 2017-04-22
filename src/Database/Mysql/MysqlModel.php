<?php

namespace Sifo\Database\Mysql;

use Sifo\Config;
use Sifo\Debug\Mysql;
use Sifo\Http\Domains;
use Sifo\I18N;
use Sifo\MysqlDebug;
use Sifo\Registry;
use Sifo\unknown;

class MysqlModel
{
    protected $db;

    /**
     * Use this method as constructor in children.
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
        Registry::getInstance()->set($key, $value);
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

    /**
     * Returns the Database connection object.
     *
     * @param string $profile The profile to be used in the database connection.
     *
     * @return \Sifo\Database\Mysql\Mysql|MysqlDebug
     */
    protected function connectDb($profile = 'default')
    {
        if (Domains::getInstance()->getDebugMode() !== true)
        {
            return Mysql::getInstance($profile);
        }

        return Mysql::getInstance($profile);
    }

    /**
     * Magic method to retrieve table names from a configuration file.
     *
     * @param string $attribute
     *
     * @return string
     */
    public function __get($attribute)
    {
        $tablenames = Config::getInstance()->getConfig('tablenames');

        $domain = Domains::getInstance()->getDomain();

        if (isset($tablenames['names'][$domain][$attribute]))
        {
            return $tablenames['names'][$domain][$attribute];
        }
        elseif (isset($tablenames['names']['default'][$attribute]))
        {
            return $tablenames['names']['default'][$attribute];
        }

        return $attribute;
    }
}
