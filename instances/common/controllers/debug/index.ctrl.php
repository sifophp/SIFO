<?php
namespace Common;

use Sifo\DebugDataBaseHandler;

class FilterCookieDebug extends \Sifo\FilterCookie
{

	public static function getCookiesArray()
	{
		$all_cookies = ( self::getInstance()->request );

		$uncommon_cookies = array();
		foreach ( $all_cookies as $key => $value )
		{
			if ( preg_match( "/^[^__]/", $key ) )
			{
				$uncommon_cookies[$key] = $value;
			}
		}

		return ( isset( $uncommon_cookies ) && count( $uncommon_cookies ) )? $uncommon_cookies : null;
	}
}

class DebugIndexController extends \Sifo\Controller
{
	/**
	 * @var string Identifier of the current request execution.
	 */
	protected $execution_key;

	/**
	 * Constructs an structured array ($debug_data) with one key for each debug module.
	 * We'll store this array on the DebugDataBaseHandler indexed by the $this->execution_key and,
	 * at the moment of recover all the debug modules data in order to show the complete debug info,
	 * we'll recover this array and fetch the templates stored in the debug/analyzer_modules/*.tpl path based on the module name matching.
	 *
	 * Example: We set a debug module called smarty_errors, so we need a $debug_data array key with all these info and
	 * a template in debug/smarty_errors.tpl in order to render and show this data in the debug execution analyzer.
	 */
	public function build()
	{
		$total_script_execution = \Sifo\Benchmark::getInstance()->timingCurrent();

		$this->setLayout( 'debug/mini_debug.tpl' );

		$this->execution_key = md5( time() . rand() );
		$params = $this->getParams();

		$this->assign( 'show_timers', isset( $params['show_debug_timers'] ) ? $params['show_debug_timers'] : true );
		$this->assign( 'execution_key', $this->execution_key );
		\Sifo\Debug::setExecutionKey( $this->execution_key );

		// Headers:
		$debug_data['headers'] = \Sifo\Headers::getDebugInfo();

		// Basic Debug data:
		$debug_data['controllers'] = \Sifo\Debug::get( 'controllers' );
		$debug_data['benchmarks']  = \Sifo\Debug::get( 'benchmarks' );
		$debug_data['elements']    = \Sifo\Debug::get( 'elements' );

		// Execution times
		$debug_data['times']       = \Sifo\Debug::get( 'times' );

		if ( !isset( $debug_data['times']['cache'] ) ) $debug_data['times']['cache'] = 0;
		if ( !isset( $debug_data['times']['external'] ) ) $debug_data['times']['external'] = 0;
		if ( !isset( $debug_data['times']['db_connections'] ) ) $debug_data['times']['db_connections'] = 0;
		if ( !isset( $debug_data['times']['db_queries'] ) ) $debug_data['times']['db_queries'] = 0;
		if ( !isset( $debug_data['times']['search'] ) ) $debug_data['times']['search'] = 0;
		if ( !isset( $debug_data['times']['sphinxql'] ) ) $debug_data['times']['sphinxql'] = 0;

		$debug_data['times']['total'] = $total_script_execution;
		$debug_data['times']['scripts'] = $debug_data['times']['total'] - ( $debug_data['times']['db_connections'] + $debug_data['times']['db_queries'] + $debug_data['times']['search'] + $debug_data['times']['cache'] + $debug_data['times']['external'] + $debug_data['times']['sphinxql'] );

		// Smarty Debug:
		$debug_data['smarty_errors'] = \Sifo\Debug::get( 'smarty_errors' );

		// Database debug.
		$debug_data['queries']            = \Sifo\Debug::get( 'queries' );
		$debug_data['queries_errors']     = \Sifo\Debug::get( 'queries_errors' );
		$debug_data['queries_duplicated'] = \Sifo\Debug::get( 'duplicated_queries' );

		// Search debug.
		$debug_data['search'] = \Sifo\Debug::get( 'searches' );

		// SphinxQl debug.
		$debug_data['sphinxql']        = \Sifo\Debug::get( 'sphinxql' );
		$debug_data['sphinxql_errors'] = \Sifo\Debug::get( 'sphinxql_errors' );

		// Redis debug.
		$debug_data['redis'] = \Sifo\Debug::get( 'redis' );

		// Environment variables:
		$debug_data['post']    = $this->getPostData();
		$debug_data['session'] = $this->getSessionData();
		$debug_data['cookies'] = FilterCookieDebug::getCookiesArray();

		// Debug messages:
		$debug_data['log_messages'] = \Sifo\Debug::get( 'log_messages' );
		$this->assign( 'log_messages', $debug_data['log_messages'] );

		// Summary debug:
		$debug_data['rebuild_all'] = $this->isRebuildAllActive();

		$debug_data['memory_usage'] = $this->getMemoryUsage();

		$this->assign( 'debug', $debug_data );

		$this->assign( 'url', $this->getParam( 'url' ) );

		$this->finalRender( $debug_data );
	}

	private function finalRender( $debug_data )
	{
		$database_debug_handler = new DebugDataBaseHandler();

		$database_debug_handler->saveExecutionDebug( $this->execution_key, $this->getDebugUrl(), $debug_data, $this->getParam( 'executed_controller_is_json' ) );

		$database_debug_handler->cleanOldExecutionDebugs();
	}

	protected function getDebugUrl()
	{
		return \Sifo\Urls::$base_url . \Sifo\FilterServer::getInstance()->getString( 'REQUEST_URI' );
	}

	private function getPostData()
	{
		return \Sifo\FilterPost::getInstance()->getRawRequest();
	}

	private function getSessionData()
	{
		return ( isset( $_SESSION ) && count( $_SESSION ) )? $_SESSION : null;
	}

	private function isRebuildAllActive()
	{
		return \Sifo\FilterCookie::getInstance()->getInteger( 'rebuild_all' );
	}

	private function getMemoryUsage()
	{
		if( !function_exists('memory_get_usage') )
		{
			if ( substr(PHP_OS,0,3) == 'WIN')
			{
				$output = array();
				exec( 'tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output );

				$memory_usage = preg_replace( '/[\D]/', '', $output[5] ) * 1024;
			}
			else
			{
				$pid = getmypid();
				exec("ps -eo%mem,rss,pid | grep $pid", $output);
				$output = explode("  ", $output[0]);

				$memory_usage = $output[1] * 1024;
			}
		}else{
			$memory_usage = memory_get_usage( true );
		}

		return number_format( $memory_usage, 0, ",", "." ) . ' bytes';
	}
}