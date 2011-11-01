<?php
/**
 * LICENSE
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
 *
 */

/**
 * Class that keeps messages of errors, success, etc... in the registry.
 */
class FlashMessages
{
	const MSG_KO = 'msg_ko';
	const MSG_OK = 'msg_ok';
	const MSG_WARNING = 'msg_warning';
	const MSG_INFO = 'msg_info';

	/**
	 * Store the message in registry.
	 *
	 * @param mixed $message The message string or an error list (array).
	 * @param string $type The class associated to this message, depending on the result.
	 */
	static public function set( $message, $type = self::MSG_OK )
	{
		switch ( $type )
		{
			case self::MSG_KO:
			case self::MSG_OK:
			case self::MSG_WARNING:
			case self::MSG_INFO:
				break;
			default:
				throw new Exception( 'Unknow type of FlashMessage' );

		}

		$registry = Registry::getInstance();
		if ( $registry->keyExists( 'flash_messages' ) )
		{
			$flash_messages = $registry->get( 'flash_messages' );
		}

		if ( is_array( $message ) ) // Dump of errors.
		{
			$flash_messages[$type] = $message;
		}
		else
		{
			$flash_messages[$type][] = $message;
		}
		$registry->set( "flash_messages", $flash_messages );
	}

	/**
	 * Returns the messages stack.
	 */
	static public function get( $type = null )
	{
		$messages = array();
		$existing_messages = self::_getMsgs();

		if ( null === $type )
		{
			return $existing_messages;
		}
		else
		{
			if ( isset( $existing_messages[$type] ) )
			{
				return $existing_messages[$type];
			}
			return false;
		}
	}

	/**
	 * Get the messages stack.
	 *
	 * @return array
	 */
	static private function _getMsgs()
	{
		$msgs = Registry::getInstance()->get( 'flash_messages');

		if ( $msgs )
		{
			return $msgs;
		}

		return array();
	}
}