<?php
/**
 * Created by PhpStorm.
 * User: obokaman
 * Date: 22/4/17
 * Time: 13:53
 */
namespace Sifo\Database;

class LoadBalancerSearch extends LoadBalancer
{
    /**
     * Name of the cache where the results of server status are stored.
     *
     * @var string
     */
    public $loadbalancer_cache_key = '__sphinx_loadbalancer_available_nodes';

    protected function addNodeIfAvailable($index, $node_properties)
    {
        try
        {
            Search::connect($node_properties);
            $this->addServer($index, $node_properties['weight']);
        }
        catch (\Sifo\Exception_500 $e)
        {
            trigger_error('Sphinx (' . $node_properties['server'] . ':' . $node_properties['port'] . ') is down!');
        }
    }
}
