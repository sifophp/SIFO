<?php
/**
 * LICENSE
 *
 * Copyright 2013 Pablo Ros
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

use Sifo\Exception\SEO\Exception500;

/**
 * Class LoadBalancerSphinxql
 * @package Sifo
 */
class LoadBalancerSphinxql extends LoadBalancer
{
    /**
     * Name of the cache where the results of server status are stored.
     * @var string
     */
    public $loadbalancer_cache_key = '__sphinxql_loadbalancer_available_nodes';

    private $sphinxql_object;

    protected function addNodeIfAvailable( $index, $node_properties )
    {
        try
        {
            $this->sphinxql_object->connect( $node_properties );
            $this->addServer( $index, $node_properties['weight'] );
        }
        catch( Exception500 $e )
        {
            trigger_error( 'Sphinx (' . $node_properties['server'] . ':' . $node_properties['port'] . ') is down!' );
        }
    }

    public function injectObject( $object )
    {
        $this->sphinxql_object = $object;
    }
}