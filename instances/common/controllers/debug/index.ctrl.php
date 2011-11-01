<?php
class FilterCookieDebug extends FilterCookie
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

class DebugIndexController extends Controller
{
	public function build()
	{
		$this->setLayout( 'debug.tpl' );
		$debug['traces']			= Registry::getInstance()->get( 'trace_messages' );
		$debug['controllers']		= Registry::getInstance()->get( 'debug' );
		$debug['benchmarks']		= Registry::getInstance()->get( 'benchmarks' );
		$debug['elements']			= Registry::getInstance()->get( 'elements' );
		$debug['times']				= Registry::getInstance()->get( 'times' );
		$debug['queries']			= Registry::getInstance()->get( 'queries' );
		$debug['queries_errors']	= Registry::getInstance()->get( 'queries_errors' );
		$debug['searches']			= Registry::getInstance()->get( 'searches' );
		$debug['session']			= $this->getSessionData();
		$debug['cookies']			= FilterCookieDebug::getCookiesArray();

		$debug['rebuild_all']		= $this->isRebuildAllActive();

		$debug['times']['total']	= Benchmark::getInstance()->timingCurrent();
		if ( !isset( $debug['times']['cache'] ) ) $debug['times']['cache'] = 0;
		if ( !isset( $debug['times']['external'] ) ) $debug['times']['external'] = 0;
		if ( !isset( $debug['times']['db_connections'] ) ) $debug['times']['db_connections'] = 0;
		if ( !isset( $debug['times']['db_queries'] ) ) $debug['times']['db_queries'] = 0;
		if ( !isset( $debug['times']['search'] ) ) $debug['times']['search'] = 0;

		$debug['times']['scripts']	= $debug['times']['total'] - ( $debug['times']['db_connections'] + $debug['times']['db_queries'] + $debug['times']['search'] + $debug['times']['cache'] + $debug['times']['external'] );

		$debug['memory_usage']		= $this->getMemoryUsage();

		$this->assign( 'debug', $debug );
	}

	private function getSessionData()
	{
		return ( isset( $_SESSION ) && count( $_SESSION ) )? $_SESSION : null;
	}

	private function isRebuildAllActive()
	{
		return FilterCookie::getInstance()->getInteger( 'rebuild_all' );
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