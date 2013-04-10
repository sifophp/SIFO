<?php
namespace Common;

include_once ROOT_PATH . '/instances/common/controllers/shared/commandLine.ctrl.php';

class ScriptsSendLogController extends SharedCommandLineController
{
	private $_parsed_errors=array();
	private $_from_time;
	private $_dump_html;

	private function _sortErrorsArray()
	{
		$array_reference =  array();
		foreach ( $this->_parsed_errors as $error )
		{
			$array_reference[] = $error['count'];
		}
		array_multisort( $array_reference, SORT_DESC, $this->_parsed_errors );
	}
	
	/**
	 * Group the errors counting the repetitions.
	 *
	 * @param $date_time Is the date time ( in timestamp format) used to starts the agrupation.
	 */  ///\[(.+)\]\sPHP\s([Notice|Warning|Error].* )/
	private function _groupErrors( $date_time = 0 )
	{
		$error_log_path =  ROOT_PATH . '/logs/errors.log';
		$error_lines = file( $error_log_path, FILE_SKIP_EMPTY_LINES);
		if ( is_array( $error_lines ) ) {
			foreach ( $error_lines as $line )
			{
				if ( preg_match( "/\[(.+)\]\sPHP\s([Notice|Warning|Error].* )/", $line , $matchs ) )
				{
					$last_date = $matchs[1];
					// Verify if the date is gt date_time.
					if ( strtotime( $last_date ) > $date_time )
					{
						$last_error = $matchs[2];
						if ( isset( $this->_parsed_errors[$last_error] ) )
						{
							$this->_parsed_errors[$last_error]['count']++;
							$this->_parsed_errors[$last_error]['finish_date'] = $last_date;
						}
						else
						{
							$this->_parsed_errors[$last_error]['count'] = 1;
							$this->_parsed_errors[$last_error]['init_date'] = $last_date;
							$this->_parsed_errors[$last_error]['finish_date'] = $last_date;
						}
					}
				}
			}
		}
		$this->_sortErrorsArray();
		unset( $error_lines );

	}


	private function _showErrors( $is_html )
	{
		if ( $is_html )
		{
			$this->showMessage( "<table>", self::ALL, false );
		}
		foreach ( $this->_parsed_errors as $error_msg => $error_params )
		{
			if ( $is_html )
			{
				$this->showMessage( "<tr>", self::ALL, false );
				$this->showMessage( "<td>" . $error_params['count'] . "</td><td>" . $error_params['init_date'] . "</td><td>" . $error_params['finish_date'] . "</td><td>" . $error_msg . "</td>", self::ALL, false );
				$this->showMessage( "</tr>", self::ALL, false );
			}
			else
			{
				$this->showMessage( $error_params['count'] . "\t" . $error_params['init_date'] . "\t" . $error_params['finish_date'] . "\t" . $error_msg );
			}
		}
		if ( $is_html )
		{
			$this->showMessage( "</table>", self::ALL, false );
		}
	}
	// ABSTRACTED METHODES:

	public function init()
	{
		$this->help_str = 'This script show the last messages in the SIFO log. Use the time option to set the time ago option. '.PHP_EOL;
		$this->help_str .= 'If you want receive the report to your mailbox you can use the --recipient option. '.PHP_EOL;

		$this->setNewParam( 'T', 'time', 'Define, in seconds, the time from which you want receive the log messages.', true, true );
		$this->setNewParam( 'H', 'html', 'Use this option for dump the info in html format.', false, false );
	}
	
	public function exec()
	{
		$this->showMessage( "Starting the script", self::VERBOSE );
		foreach ( $this->command_options as $option )
		{
			switch ( $option[0] )
			{
				case "H":
				case "html":
					$this->_dump_html = true;
					break;
				case "T":
				case "time":
					$this->_from_time = time() - $option[1];
					break;
			}
		}
		$this->_groupErrors( $this->_from_time );

		if ( $this->_dump_html )
		{
			$this->_showErrors( true );
		}
		else
		{
			$this->_showErrors( false );
		}

		$this->showMessage( "Finishing!", self::VERBOSE );
	}
}
?>