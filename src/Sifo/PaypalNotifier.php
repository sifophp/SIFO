<?php
/**
 * LICENSE
 *
 * Copyright 2011 Quim Blanch
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

require_once ROOT_PATH . '/libs/Paypal/Ipn.php';

/**
 * Adapter for PayPal notifier.
 */
class PaypalNotifier extends \IpnListener
{
	public function __construct()
	{
		$config = Config::getInstance()->getConfig( 'paypal' );

		return parent::__construct(
			$config['use_curl'],
			$config['force_ssl_v3'],
			$config['follow_location'],
			$config['use_ssl'],
			$config['use_sandbox'],
			$config['timeout']
		);
	}
}
