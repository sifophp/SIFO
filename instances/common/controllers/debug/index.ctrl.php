<?php
namespace Common;

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

	protected $debug_modules = array();

	protected $execution_key;

	public function build()
	{
		$this->setLayout( 'debug/debug.tpl' );

		$this->execution_key = md5( time() . rand() );

		// Headers:
		$debug['headers']	 		= \Sifo\Headers::getDebugInfo();
		$this->renderDebugModule( $debug, 'headers', 'debug/headers.tpl' );


		// Basic Debug data:
		$debug['controllers']		= \Sifo\Debug::get( 'controllers' );
		$debug['benchmarks']		= \Sifo\Debug::get( 'benchmarks' );
		$debug['elements']			= \Sifo\Debug::get( 'elements' );
		$debug['times']				= \Sifo\Debug::get( 'times' );
		$this->renderDebugModule( $debug, 'basic_debug', 'debug/basic_debug.tpl' );

		// Smarty Debug:
		$debug['smarty_errors']		= \Sifo\Debug::get( 'smarty_errors' );
		$this->assign( 'debug', $debug );
		$this->renderDebugModule( $debug, 'smarty_errors', 'debug/smarty_errors.tpl' );

		// Database debug.
		$debug['queries']				= \Sifo\Debug::get( 'queries' );
		$debug['queries_errors']		= \Sifo\Debug::get( 'queries_errors' );
		$debug['queries_duplicated']	= \Sifo\Debug::get( 'duplicated_queries' );
		$this->assign( 'debug', $debug );
		$this->renderDebugModule( $debug, 'database', 'debug/database.tpl' );

		// Search debug.
		$debug['searches']				= \Sifo\Debug::get( 'searches' );
		$this->assign( 'debug', $debug );
		$this->renderDebugModule( $debug, 'search', 'debug/search.tpl' );

		// Environment variables:
		$debug['post']				= $this->getPostData();
		$debug['session']			= $this->getSessionData();
		$debug['cookies']			= FilterCookieDebug::getCookiesArray();

		// Debug messages:
		$debug['log_messages']  		= \Sifo\Debug::get( 'log_messages' );
		$this->assign( 'log_messages', $debug['log_messages'] );
		$this->renderDebugModule( $debug, 'log_messages', 'debug/log_messages.tpl' );

		// Summary debug:
		$debug['rebuild_all']		= $this->isRebuildAllActive();
		$debug['times']['total']	= \Sifo\Benchmark::getInstance()->timingCurrent();

		if ( !isset( $debug['times']['cache'] ) ) $debug['times']['cache'] = 0;
		if ( !isset( $debug['times']['external'] ) ) $debug['times']['external'] = 0;
		if ( !isset( $debug['times']['db_connections'] ) ) $debug['times']['db_connections'] = 0;
		if ( !isset( $debug['times']['db_queries'] ) ) $debug['times']['db_queries'] = 0;
		if ( !isset( $debug['times']['search'] ) ) $debug['times']['search'] = 0;

		$debug['times']['scripts']	= $debug['times']['total'] - ( $debug['times']['db_connections'] + $debug['times']['db_queries'] + $debug['times']['search'] + $debug['times']['cache'] + $debug['times']['external'] );

		$debug['memory_usage']		= $this->getMemoryUsage();

		$params = $this->getParams();
		$this->assign( 'show_timers', isset( $params['show_debug_timers'] ) ? $params['show_debug_timers'] : true );
		$this->assign( 'execution_key', $this->execution_key );

		$this->finalRender( $debug );
	}

	protected function renderDebugModule( $debug, $module_name, $template)
	{
		$this->assign( 'debug', $debug );
		$this->assign( 'execution_key', $this->execution_key );
		$this->debug_modules[$module_name] = $this->fetch( $template );
	}

	protected function finalRender( $debug )
	{
		$this->assign( 'debug', $debug );
		$this->assign( 'debug_modules', $this->debug_modules );
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

?>