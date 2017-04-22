<?php

namespace Sifo;

use Sifo\Http\Session;

/**
 * Class that keeps messages of errors, success, etc... in the registry.
 */
class FlashMessages
{
    const MSG_KO = 'msg_ko';
    const MSG_OK = 'msg_ok';
    const MSG_WARNING = 'msg_warning';
    const MSG_INFO = 'msg_info';

    const STORAGE_REGISTRY = 1;
    const STORAGE_SESSION = 2;

    /**
     * Store the message in registry.
     *
     * @param mixed   $message          The message string or an error list (array).
     * @param string  $type             The class associated to this message, depending on the result.
     * @param boolean $store_in_session Whether the message is stored in registry or in session.
     */
    static public function set($message, $type = self::MSG_OK, $storage_engine = self::STORAGE_REGISTRY)
    {
        switch ($type)
        {
            case self::MSG_KO:
            case self::MSG_OK:
            case self::MSG_WARNING:
            case self::MSG_INFO:
                break;
            default:
                throw new \Exception('Unknown type of FlashMessage');
        }

        $registry = self::_getStorageEngine($storage_engine);

        if ($registry->keyExists('flash_messages'))
        {
            $flash_messages = $registry->get('flash_messages');
        }

        if (is_array($message)) // Dump of errors.
        {
            $flash_messages[$type] = $message;
        }
        else
        {
            $flash_messages[$type][] = $message;
        }
        $registry->set("flash_messages", $flash_messages);
    }

    /**
     * Returns the messages stack.
     */
    static public function get($type = null, $storage_engine = self::STORAGE_REGISTRY)
    {
        $messages          = array();
        $existing_messages = self::_getMsgs($storage_engine);

        if (null === $type)
        {
            return $existing_messages;
        }
        else
        {
            if (isset($existing_messages[$type]))
            {
                return $existing_messages[$type];
            }

            return false;
        }
    }

    /**
     * Get the messages stack.
     *
     * @param integer $storage_engine The storage engine to retrieve the msgs.
     *
     * @return array
     */
    static private function _getMsgs($storage_engine)
    {
        $registry = self::_getStorageEngine($storage_engine);

        $msgs = $registry->get('flash_messages');
        if ($msgs && $storage_engine === self::STORAGE_SESSION)
        {
            $registry->delete('flash_messages');
        }

        if ($msgs)
        {
            return $msgs;
        }

        return array();
    }

    static private function _getStorageEngine($engine)
    {
        switch ($engine)
        {
            case self::STORAGE_SESSION:
                return Session::getInstance();
            case self::STORAGE_REGISTRY:
                return Registry::getInstance();
            default:
                throw new Exception_503('Invalid storage type.');
        }
    }
}
