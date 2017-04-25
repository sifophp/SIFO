<?php

namespace Sifo\Database\Sphinx;

use Sifo\Database\LoadBalancer;
use Sifo\Exception\Http\InternalServerError;

class LoadBalancerSphinxql extends LoadBalancer
{
    /** @var string */
    public $loadbalancer_cache_key = '__sphinxql_loadbalancer_available_nodes';

    /** @var Sphinxql */
    private $sphinxql_object;

    protected function addNodeIfAvailable($index, $node_properties)
    {
        try {
            $this->sphinxql_object->connect($node_properties);
            $this->addServer($index, $node_properties['weight']);
        } catch (InternalServerError $e) {
            trigger_error('Sphinx (' . $node_properties['server'] . ':' . $node_properties['port'] . ') is down!',
                E_ERROR);
        }
    }

    public function injectObject($object)
    {
        $this->sphinxql_object = $object;
    }
}
