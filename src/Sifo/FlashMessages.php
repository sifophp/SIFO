<?php

/**
 * LICENSE.
 *
 * Copyright 2010 Albert Lombarte
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Sifo;

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
     * @param mixed  $message          The message string or an error list (array).
     * @param string $type             The class associated to this message, depending on the result.
     * @param bool   $store_in_session Whether the message is stored in registry or in session.
     */
    public static function set($message, $type = self::MSG_OK, $storage_engine = self::STORAGE_REGISTRY)
    {
        switch ($type) {
            case self::MSG_KO:
            case self::MSG_OK:
            case self::MSG_WARNING:
            case self::MSG_INFO:
                break;
            default:
                throw new \Exception('Unknown type of FlashMessage');

        }

        $registry = self::_getStorageEngine($storage_engine);

        if ($registry->keyExists('flash_messages')) {
            $flash_messages = $registry->get('flash_messages');
        }

        if (is_array($message)) {
            // Dump of errors.

            $flash_messages[$type] = $message;
        } else {
            $flash_messages[$type][] = $message;
        }
        $registry->set('flash_messages', $flash_messages);
    }

    /**
     * Returns the messages stack.
     */
    public static function get($type = null, $storage_engine = self::STORAGE_REGISTRY)
    {
        $messages = array();
        $existing_messages = self::_getMsgs($storage_engine);

        if (null === $type) {
            return $existing_messages;
        } else {
            if (isset($existing_messages[$type])) {
                return $existing_messages[$type];
            }

            return false;
        }
    }

    /**
     * Get the messages stack.
     *
     * @param int $storage_engine The storage engine to retrieve the msgs.
     *
     * @return array
     */
    private static function _getMsgs($storage_engine)
    {
        $registry = self::_getStorageEngine($storage_engine);

        $msgs = $registry->get('flash_messages');
        if ($msgs && $storage_engine === self::STORAGE_SESSION) {
            $registry->delete('flash_messages');
        }

        if ($msgs) {
            return $msgs;
        }

        return array();
    }

    private static function _getStorageEngine($engine)
    {
        switch ($engine) {
            case self::STORAGE_SESSION:
                return Session::getInstance();
            case self::STORAGE_REGISTRY:
                return Registry::getInstance();
            default:
                throw new Exception_503('Invalid storage type.');
        }
    }
}
