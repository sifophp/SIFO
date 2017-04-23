<?php

namespace Sifo\Http;

class Session
{

    static private $instance;

    private function __construct()
    {
        // Init session cookie domain to allow sharing session
        // across multiple subdomains.
        ini_set('session.cookie_domain', '.' . Domains::getInstance()->getDomain());

        if (!isset($_SESSION)) {
            if (headers_sent()) {
                trigger_error("Session: The session was not started before the sending of the headers.");

                return false;
            } else {
                // Session init.
                session_start();
            }
        }
    }

    /**
     * Singleton
     *
     * @static
     * @return Session
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Use it to set a single var like $ses->set( 'name', 'val' ); or an array of pairs key-value like $ses->set( array( 'key' => 'val' ) );
     *
     * @param string|array $name
     * @param string|null $value
     *
     * @return boolean
     */
    public function set($name, $value = null)
    {
        if (is_array($name) && null === $value) {
            foreach ($name as $key => $val) {
                $_SESSION[$key] = $val;
            }
        } elseif (!isset($name) || !isset($value)) {
            trigger_error("Session: Missing parameter or parameters.");

            return false;
        } else {
            $_SESSION[$name] = $value;
        }

        return true;
    }

    public function get($name)
    {
        if (!isset($_SESSION[$name])) {
            return null;
        }

        return $_SESSION[$name];
    }

    public function getId()
    {
        if (!isset($_SESSION)) {
            return null;
        }

        return session_id();
    }

    public function delete($name)
    {
        if (!isset($_SESSION[$name])) {
            trigger_error("Session: $name variable does not exist.");

            return false;
        } else {
            unset($_SESSION[$name]);

            return true;
        }
    }

    /**
     * @param string $index
     *
     * @returns boolean
     *
     */
    public static function keyExists($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove all the session saved data. And the Session continue started.
     *
     * @return bool
     */
    public function reset()
    {
        $this->destroy();
        session_start();

        return true;
    }

    public function destroy()
    {
        if (isset($_SESSION)) {
            unset($_SESSION);
            $_SESSION = array();
            session_destroy();
        }

        return true;
    }

    /**
     * Ends the current session and store session data.
     */
    public function writeClose()
    {
        session_write_close();
    }

    public static function setExpirationTime($time)
    {
        ini_set('session.cache_expire', $time);
    }
}
