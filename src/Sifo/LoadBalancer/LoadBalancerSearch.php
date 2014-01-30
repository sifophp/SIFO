<?php
/**
 * LICENSE
 *
 * Copyright 2010 Albert Garcia
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
use Sifo\Search;

class LoadBalancerSearch extends LoadBalancer
{
    /**
     * Name of the cache where the results of server status are stored.
     * @var string
     */
    public $loadbalancer_cache_key = '__sphinx_loadbalancer_available_nodes';

    protected function addNodeIfAvailable( $index, $node_properties )
    {
        try
        {
            Search::connect( $node_properties );
            $this->addServer( $index, $node_properties['weight'] );
        }
        catch( Exception500 $e )
        {
            trigger_error( 'Sphinx (' . $node_properties['server'] . ':' . $node_properties['port'] . ') is down!' );
        }
    }
}