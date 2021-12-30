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

namespace Sifo;

/**
 * SphinxQL class. Use this class to execute queries against SphinxQL.
 * It Only supports SELECT sentences to execute.
 */
class Sphinxql
{
	/**
	 * @var Sphinxql Current instance.
	 */
	static protected $instance;

	/**
	 * @var Sphinxql Object. The Sphinxql object instance.
	 */
	public $sphinxql;

	/**
	 * @var array Sphinx config params.
	 */
	protected $sphinx_config;

	/**
	 * @var string Multi query string.
	 */
	private string $multi_query = '';

	/**
	 * Query options.
	 *
	 * Defines the options in the OPTION SphinxQL clause.
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * Allowed query options.
	 *
	 * @see http://sphinxsearch.com/docs/current.html#sphinxql-select for more information.
	 * @var array
	 */
	protected $allowed_options = array(
		'agent_query_timeout', // integer, max time in milliseconds to wait for remote queries to complete.
		'boolean_simplify', // 0 or 1, enables simplifying the query to speed it up.
		'comment', // string, user comment that gets copied to a query log file.
		'cutoff', // integer, max found matches threshold.
		'field_weights', // a named integer list, per-field user weights for ranking, pe: "( title = 10, body = 5 )".
		'index_weights', // a named integer list, per-index user weights for ranking.
		'max_matches', // integer, per-query max matches value.
		'max_query_time', // integer, max search time threshold in msec.
		'max_predicted_time', // integer, max predicted search time.
		'ranker', // any of 'proximity_bm25', 'bm25', 'none', 'wordcount', 'proximity', 'matchany', 'fieldmask', 'sph04', 'expr', or 'export'.
		'retry_count', // integer distributed retries count.
		'retry_delay', // integer distributed retry delay in msec.
		'reverse_scan', // 0 or 1, lets you control the order in which full-scan query processes the rows.
		'global_idf', // use global statistics (frequencies) from the global_idf file for IDF computations, >= 2.1.1-beta.
		'idf', // a quoted, comma-separated list of IDF computation flags, >= 2.1.1-beta.
		'sort_method' // 'pq' priority queue (set by default) or 'kbuffer' gives faster sorting for already pre-sorted data, >= 2.1.1-beta.
	);

	/**
	 * Initialize this class.
	 * @param $profile
	 */
	protected function __construct( $profile )
	{
		$this->sphinx_config = $this->getConnectionParams( $profile );

		// Check if Sphinx is enabled by configuration:
		if ( true === $this->sphinx_config['active'] )
		{
			$this->sphinxql = $this->connect( $this->sphinx_config );
		}
	}

	/**
	 * Singleton get instance. Return one search engine object.
	 *
	 * @param string $profile
	 * @return Sphinxql Config
	 */
	public static function getInstance( $profile = 'default' )
	{
		if ( !isset ( self::$instance[$profile] )  )
		{
			if ( Domains::getInstance()->getDebugMode() !== true )
			{
				self::$instance[$profile] = new Sphinxql( $profile );
			}
			else
			{
				self::$instance[$profile] = new DebugSphinxql( $profile );
			}
		}

		return self::$instance[$profile];
	}

	/**
	 * Get Sphinx connection params from config files.
	 *
	 * @param $profile
	 * @throws Exception_500
	 * @return array
	 */
	protected function getConnectionParams( $profile )
	{
		try
		{
			// If the domains.config doesn't define the params, we use the sphinx.config.
			$sphinx_config = Config::getInstance()->getConfig( 'sphinx' );

			if ( empty( $sphinx_config[$profile] ) )
			{
				throw new \Sifo\Exception_500( "Expected sphinx settings not defined for profile {$profile} in sphinx.config." );
			}

			$sphinx_config = $this->checkBalancedProfile( $sphinx_config[$profile] );
		}
		catch ( Exception_Configuration $e )
		{
			throw new \Sifo\Exception_500( 'You must define the connection params in sphinx.config' );
		}

		return $sphinx_config;
	}

	/**
	 * Check if one profile has balanced servers or single server. Returns the connection to use.
	 * @param $sphinx_config
	 * @return array
	 */
	private function checkBalancedProfile( $sphinx_config )
	{
		if ( isset( $sphinx_config[0] ) && is_array( $sphinx_config[0] ) )
		{
			$lb = new LoadBalancerSphinxql();
			$lb->injectObject( $this );
			$lb->setNodes( $sphinx_config );
			$selected_server = $lb->get();
			$sphinx_config = $sphinx_config[$selected_server];
		}

		return $sphinx_config;
	}

	/**
	 * Use this method to connect to Sphinx.
	 * @param $node_properties
	 * @return \mysqli
	 * @throws Exception_500
	 */
	public function connect( $node_properties )
	{
		$mysqli = @mysqli_connect( $node_properties['server'], '', '', '', $node_properties['port'] );

		if ( !$mysqli || $mysqli->connect_error )
		{
			throw new \Sifo\Exception_500( 'Sphinx (' . $node_properties['server'] . ':' . $node_properties['port'] . ') connection error: ' . mysqli_connect_error() );
		}

		return $mysqli;
	}

	/**
	 * Executes one query (selects) into sphinxQl.
	 * @param $query
	 * @param $tag
	 * @param array $parameters
	 * @return array|boolean
	 */
	public function query( $query, $tag = null, $parameters = array() )
	{
		$this->addQuery( $query, $tag, $parameters );

		$results = $this->multiQuery( $tag );

		// If we called this method we expect only one result...
		if ( !empty( $results ) )
		{
			// ...so we pop it from the resultset.
			return array_pop( $results );
		}

		return $results;
	}

	/**
	 * Add query to be executed using the multi query feature.
	 * @param $query
	 * @param null $tag
	 * @param array $parameters
	 *
	 * @return string The query after being prepared.
	 */
	public function addQuery( $query, $tag = null, $parameters = array() )
	{
		// Delete final ; because is the query separator for multi queries.
		$query = preg_replace( '/;+$/', '', $query );

		$query = $this->appendOptionsToQuery( $query, $this->options );
		$query = $this->prepareQuery( $query, $parameters );

		$this->multi_query .= $query;
		if ( !preg_match( '/^CALL|^INSERT|^DELETE|^REPLACE/i', $query ) )
		{
			$this->multi_query .= ';';
		}

		$this->resetOptions();

		return $query;
	}

	/**
	 * Execute all queries added using addQuery method.
	 * @param $tag
	 * @return array|boolean
	 */
	public function multiQuery( $tag = null )
	{
		$final_result = false;
		$response = $this->sphinxql->multi_query( $this->multi_query );

		if ( !$response || $this->sphinxql->errno )
		{
			$this->logError( $this->sphinxql->error );
		}
		else
		{
			do
			{
				if ( $result = $this->sphinxql->store_result() )
				{
					for ( $res = array(); $tmp = $result->fetch_array( MYSQLI_ASSOC ); ) $res[] = $tmp;
					$final_result[] = $res;
					$result->free();
				}
			}
			while ( $this->sphinxql->more_results() && $this->sphinxql->next_result() );
		}

		$this->multi_query = '';

		return $final_result;
	}

	/**
	 * Some kind of PDO prepared statements simulation.
	 *
	 * Autodetects the parameter type, escapes them and replace the keys in the query. Example:
	 *
	 * 	$sql = 'SELECT * FROM index WHERE tag = :tag_name';
	 * 	$results = $sphinx->query( $sql, 'label', array( ':tag_name' => 'some-tag' ) );
	 *
	 * @param string $query SphinxQL query.
	 * @param array $parameters List of parameters.
	 *
	 * @return string Prepared query.
	 */
	protected function prepareQuery( $query, $parameters )
	{
		if ( empty( $parameters ) )
		{
			return $query;
		}

		foreach ( $parameters as &$parameter )
		{
			if ( is_null( $parameter ) )
			{
				$parameter = 'NULL';
			}
			elseif ( is_int( $parameter ) || is_float( $parameter ) )
			{
				// Locale unaware number representation.
				$parameter = sprintf( '%.12F', $parameter );
				if ( false !== strpos( $parameter, '.' ) )
				{
					$parameter = rtrim( rtrim( $parameter, '0' ), '.' );
				}
			}
			else
			{
				$parameter = "'" . $this->sphinxql->real_escape_string( $parameter ) . "'";
			}
		}

		return strtr( $query, $parameters );
	}

	/**
	 * Append the OPTION clause to the end of the query if an option list is defined.
	 *
	 * @param string $query The SphinxQL query.
	 * @param array $options Option list.
	 *
	 * @return string
	 */
	protected function appendOptionsToQuery( $query, array $options )
	{
		if ( empty( $options ) )
		{
			return $query;
		}

		$options_list = array();
		foreach ( $options as $option => $value )
		{
			$options_list[] = $option . ' = ' . $value;
		}

		return $query . "\n" . 'OPTION ' . implode( ', ', $options_list );
	}

	/**
	 * Set query options. Triggers a warning if the option is not allowed or implemented.
	 *
	 * @param array $options
	 */
	public function setOptions( array $options )
	{
		foreach ( $options as $option => $value )
		{
			if ( !in_array( $option, $this->allowed_options ) )
			{
				trigger_error( 'SphinxQL - The defined option "' . $option . '" is not allowed', E_USER_WARNING );
			}
			else
			{
				$this->options[$option] = $value;
			}
		}
	}

	/**
	 * Empty the query options list.
	 */
	public function resetOptions()
	{
		$this->options = array();
	}

	/**
	 * Return last error generated.
	 * @return mixed
	 */
	public function getError()
	{
		return $this->sphinxql->error;
	}

	/**
	 * Log error in the errors.log file.
	 * @param $error
	 */
	protected function logError( $error )
	{
        $filterServer = \Sifo\FilterServer::getInstance();
        trigger_error('[SphinxQL ERROR] ' . $error . ' in ' . $filterServer->getString('HTTP_REFERER') . ' calling ' . $filterServer->getString('SCRIPT_URI'), E_USER_WARNING);
	}
}

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
    protected $load_balancer_cache_key = 'BalancedNodesSphinxql';

    private \Sifo\Sphinxql $sphinxql_object;

	protected function addNodeIfAvailable( $index, $node_properties )
	{
		try
		{
			$this->sphinxql_object->connect( $node_properties );
			$this->addServer( $index, $node_properties['weight'] );
		}
		catch( \Sifo\Exception_500 $e )
		{
			trigger_error('[SPHINXQL LOAD BALANCER] ' . $e->getMessage(), E_USER_WARNING);
		}
	}

	public function injectObject( $object )
	{
		$this->sphinxql_object = $object;
	}
}
