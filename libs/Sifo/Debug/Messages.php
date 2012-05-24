<?php
/**
 * LICENSE
 *
 * Copyright 2011 Javier Ferrer
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

namespace Sifo;

/**
 * DebugMessages class: It lets you show debug messages in browser console from your PHP code. Code related to this behaviour in instances/common/controllers/debug/index.ctrl.php
 * How to use it:
 *
 * \Sifo\DebugMessages::log( 'This message will be shown in browser console.' );
 * \Sifo\DebugMessages::log( $variable );
 * \Sifo\DebugMessages::log( $variable_array );
 */
class DebugMessages
{
	public static function log( $message, $type = 'log' )
	{
		if ( Domains::getInstance()->getDevMode() )
		{
			switch ( $type )
			{
				case 'log':
				case 'info':
				case 'warn':
				case 'error':
				{
					$is_object = false;
					if ( is_object( $message ) || is_array( $message ) )
					{
						$is_object = true;

						$message = "'" . str_replace( "'", "\\'", json_encode( $message ) ) . "'";
					}
					else
					{
						$message = "'" . str_replace( "'", "\\'", $message ) . "'";
					}
					Registry::push( 'debug_messages', array( 'type' => $type, 'is_object' => $is_object, 'message' => $message ) );
				}break;
				default:
				{
					trigger_error( 'undefined debug type => ' . $type . ' for debug message: ' . $message );
				}break;
			}
		}
	}

}
?>