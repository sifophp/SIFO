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

namespace Sifo\LoadBalancer;

use Sifo\LoadBalancer\LoadBalancer;

class LoadBalancerADODB extends LoadBalancer
{
    protected function addNodeIfAvailable($index, $node_properties)
    {
        try {
            $db = \NewADOConnection($node_properties['db_driver']);
            $result = $db->Connect(
                $node_properties['db_host'], $node_properties['db_user'], $node_properties['db_password'],
                $node_properties['db_name']
            );

            // If no exception at this point the server is ready:
            $this->addServer($index, $node_properties['weight']);
        } catch (\ADODB_Exception $e) {
            // The server is down, won't be added in the balancing. Log it:
            trigger_error("SERVER IS DOWN! " . $node_properties['db_host']);
        }
    }
}
