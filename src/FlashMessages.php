<?php

namespace Sifo;

use Sifo\Http\Session;

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
     * @param mixed $message The message string or an error list (array).
     * @param string $type The class associated to this message, depending on the result.
     * @param int $storage_engine
     *
     * @throws \Exception
     */
    static public function set($message, $type = self::MSG_OK, $storage_engine = self::STORAGE_REGISTRY)
    {
        self::validateType($type);

        $registry = self::_getStorageEngine($storage_engine);

        if ($registry->keyExists('flash_messages')) {
            $flash_messages = $registry->get('flash_messages');
        }

        if (is_array($message)) // Dump of errors.
        {
            $flash_messages[$type] = $message;
        } else {
            $flash_messages[$type][] = $message;
        }

        $registry->set("flash_messages", $flash_messages);
    }

    static public function get($type = null, $storage_engine = self::STORAGE_REGISTRY)
    {
        $existing_messages = self::_getMsgs($storage_engine);

        if (null === $type) {
            return $existing_messages;
        }

        if (isset($existing_messages[$type])) {
            return $existing_messages[$type];
        }

        return false;
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
        $messages = $registry->get('flash_messages');

        if ($messages && $storage_engine === self::STORAGE_SESSION) {
            $registry->delete('flash_messages');
        }

        if ($messages) {
            return $messages;
        }

        return array();
    }

    static private function _getStorageEngine($engine)
    {
        switch ($engine) {
            case self::STORAGE_SESSION:
                return Session::getInstance();
            case self::STORAGE_REGISTRY:
                return Registry::getInstance();
            default:
                throw new \InvalidArgumentException('Invalid storage type.');
        }
    }

    private static function validateType($type)
    {
        if (!in_array($type, [
            self::MSG_KO,
            self::MSG_OK,
            self::MSG_WARNING,
            self::MSG_INFO
        ])
        ) {
            throw new \InvalidArgumentException('Unknown type of FlashMessage');
        }
    }
}
