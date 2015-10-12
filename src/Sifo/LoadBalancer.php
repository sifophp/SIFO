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

namespace Sifo;

use Sifo\Exception\Http\InternalServerError;

/**
 * Determines where to send the data based on server capabilities.
 *
 * Based on code of the PHP master Basi Vera.
 */
abstract class LoadBalancer
{

	/**
	 * Time in seconds the servers will be cached after checking if they are online.
	 * @var integer
	 */
	const CACHE_EXPIRATION = 60;
	/**
	 * Name of the cache where the results of server status are stored.
	 * @var string
	 */
	public $loadbalancer_cache_key = '__loadbalancer_available_nodes';

	/**
	 * Contains all the nodes available to do the load balancing.
	 * @var array
	 */
	protected $nodes;

	/**
	 * Sum of all nodes weights.
	 * @var integer
	 */
	protected $total_weights = 0;

	/**
	 * Checks if the service is currently available.
	 * @param unknown_type $index
	 * @param unknown_type $node_properties
	 */
	abstract protected function addNodeIfAvailable( $index, $node_properties );


	public function __construct()
	{
		$this->loadbalancer_cache_key = Bootstrap::$instance . $this->loadbalancer_cache_key;
	}

	/**
	 * Sets the nodes to work with.
	 * @param array $nodes
	 * @throws InternalServerError
	 * @return integer Number of nodes added.
	 */
	public function setNodes( Array $nodes )
	{
		$cache = Cache::getInstance();
		$available_servers = trim( $cache->get( $this->loadbalancer_cache_key ) ); // CacheDisk returns " " when no cache.

		if ( empty( $available_servers ) )
		{
			foreach( $nodes as $key => $node_properties )
			{
				$this->addNodeIfAvailable( $key, $node_properties );
			}

			// Save in cache available servers (even if none):
			$serialized_nodes = serialize( array( 'nodes' => $this->nodes, 'total_weights' => $this->total_weights ) );
			$cache->set( $this->loadbalancer_cache_key, $serialized_nodes, self::CACHE_EXPIRATION );
		}
		else
		{
			$available_servers = unserialize( $available_servers );
			$this->nodes = $available_servers['nodes'];
			$this->total_weights = $available_servers['total_weights'];
		}

		$num_nodes = count( $this->nodes );

		if ( 1 > $num_nodes )
		{
			// This exception will be shown for CACHE_EXPIRATION seconds until servers are up again.
			$message = "No available servers in profile";
			trigger_error( $message );
			throw new InternalServerError( $message );
		}

		return $num_nodes;


	}

	/**
	 * Adds a server to the battery of available.
	 * @param integer $index Number of server.
	 * @param integer $weight Weight of this server.
	 *
	 * @return integer Position in battery
	 */
	protected function addServer( $index, $weight )
	{
		$x = count( $this->nodes );
		$this->nodes[$x] = new \stdClass;
		$this->nodes[$x]->index = $index;
		$this->total_weights += ( $this->nodes[$x]->weight = abs( $weight ) );

		return $x;
	}

	/**
	 * Retrieves a random node, the more weight, the more chances to be the picked.
	 */
	public function get()
	{
		if ( !isset( $this->nodes ) )
		{
			throw new LoadBalancer_Exception( "There aren't any nodes set in the balancer. Have you called setNodes( Array nodes ) ?" );
		}

		$x = round( mt_rand( 0, $this->total_weights ) );

		$max = ( $i = 0 );
		do
		{
			$max += $this->nodes[ $i++ ]->weight;
		}
		while ( $x > $max );

		return $this->nodes[ ( $i-1 ) ]->index;
	}

	/**
	 * Removes a server from the list of availables.
	 *
	 * @param integer $index
	 */
	public function removeServer( $index )
	{
		if ( isset( $this->nodes[$index] ) )
		{
			$this->total_weights -= $this->nodes[$index]->weight;
			unset( $this->nodes[$index]);
			$this->nodes = array_values( $this->nodes );
		}
	}
}

class LoadBalancer_Exception extends \Exception {}