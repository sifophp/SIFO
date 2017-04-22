<?php

namespace Sifo\Database\Sphinx;

use Sifo\Database\LoadBalancer;
use Sifo\Exception_500;

/**
 * Class LoadBalancerSphinxql
 *
 * @package Sifo
 */
class LoadBalancerSphinxql extends LoadBalancer
{
    /**
     * Name of the cache where the results of server status are stored.
     *
     * @var string
     */
    public $loadbalancer_cache_key = '__sphinxql_loadbalancer_available_nodes';

    private $sphinxql_object;

    protected function addNodeIfAvailable($index, $node_properties)
    {
        try
        {
            $this->sphinxql_object->connect($node_properties);
            $this->addServer($index, $node_properties['weight']);
        }
        catch (Exception_500 $e)
        {
            trigger_error('Sphinx (' . $node_properties['server'] . ':' . $node_properties['port'] . ') is down!');
        }
    }

    public function injectObject($object)
    {
        $this->sphinxql_object = $object;
    }
}
