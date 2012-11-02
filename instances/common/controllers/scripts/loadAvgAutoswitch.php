<?php
namespace Common;

include_once ROOT_PATH . '/instances/common/controllers/shared/commandLineController.ctrl.php';

class ScriptsLoadAvgAutoswitchController extends SharedCommandLineController
{
	/**
	 * Do you need read this comment? Really????.
	 *
	 * @var string
	 */
	const PHP_OPEN_TAG = '<?php

namespace Common;';

	/**
	 * Text used to indicates the original file customization.
	 *
	 * @var string
	 */
	const ENABLED_LABEL = "//******** The replacement index file is ENABLED **************";

	/**
	 * Code used to redirect when the replacement page is enabled.
	 *
	 * @var string
	 */
	const REDIRECT_CODE = 'include( "%replacement_path%" );die();';



	private $_alternative_page_path;
	private $_load_limit;
	private $_root_page_path;

	private $_index_file_content;
	protected $subject_prefix;

	/**
	 * Return the platform index file content.
	 *
	 *	@return boolean
	 */
	private function _getFileContent()
	{
		if ( !isset( $this->_index_file_content ) )
		{
			if ( !( $this->_index_file_content = file_get_contents( $this->_root_page_path ) ) )
			{
				trigger_error( "Root file not found. Please, validate the path.", E_USER_ERROR );
			}
		}

		return $this->_index_file_content;
	}

	/**
	 * Generate the required code to enable/disable the replacement.
	 *
	 * @return string
	 */
	private function _getEnablingCode()
	{
		// Finishing with comment slashes for comment the php open label:
		return self::PHP_OPEN_TAG. PHP_EOL . self::ENABLED_LABEL . PHP_EOL . str_replace( '%replacement_path%', $this->_alternative_page_path, self::REDIRECT_CODE ) . PHP_EOL ."//";
	}

	/**
	 * Return true if the replacement is enabled.
	 *
	 * @return boolean
	 */
	private function _isEnabled()
	{
		$source = $this->_getFileContent();

		// To validate if the replacement page is enabled we can validate the replacement page path in the header of the current index file.

		return ( false !== strpos( $source, self::ENABLED_LABEL ) );
	}


	/**
	 * Return the current systema load average.
	 *
	 * @return integer
	 */
	private function _getSystemLoad()
	{
		$load_avg = sys_getloadavg();

		return $load_avg[0]; //las minute.
	}


	/**
	 * Enable the replacement page.
	 *
	 * @return boolean False when error.
	 */
	private function _enablePage()
	{
		if ( $this->test )
		{
			return true;
		}	
		$enabled_source =  $this->_getEnablingCode(). $this->_getFileContent();

		return file_put_contents( $this->_root_page_path, $enabled_source );
	}


	/**
	 * Disable the replacement page. Normal page is enabled.
	 *
	 * @return boolean False when error.
	 */
	private function _disablePage()
	{
		if ( $this->test )
		{
			return true;
		}

		$disabled_source = str_replace( $this->_getEnablingCode(), '', $this->_getFileContent() );

		return file_put_contents( $this->_root_page_path, $disabled_source );;
	}


	// ABSTRACTED METHODES:
	public function init()
	{
		$this->help_str = 'This script enable/disable a replacement main page when the current system current load average surpass a defined value. '.PHP_EOL;
		$this->help_str = 'Very usefult for show load exced pages or change the service only for premium users. '.PHP_EOL;

		$this->help_str .= 'Use the --recipient option for receive an email ever page is switched. '.PHP_EOL;

		$this->setNewParam( 'L', 'load-limit', 'Define, in seconds, the time from which you want receive the log messages.', true, true );
		$this->setNewParam( 'R', 'root-page-path', 'The first page loaded in your platform.', true, true );
		$this->setNewParam( 'A', 'alternative-page-path', 'Define the absolute path to the replacement page.', true, true );
	}
	
	public function exec()
	{
		$this->showMessage( "Starting the script", self::VERBOSE );
		foreach ( $this->command_options as $option )
		{
			switch ( $option[0] )
			{
				case "L":
				case "load-limit":
					$this->_load_limit = $option[1];
					break;
				case "R":
				case "root-page-path":
					$this->_root_page_path = $option[1];
					break;
				case "A":
				case "alternative-page-path":
					$this->_alternative_page_path = $option[1];
					break;
			}
		}
		
		$current_load = $this->_getSystemLoad();
		$this->showMessage( "The current sytem load is {$current_load}.", self::VERBOSE );

		if ( $current_load > $this->_load_limit )
		{
			$this->subject_prefix = '[KO] Load avg:'.$current_load;
			$this->showMessage( "Limt surpassed.", self::VERBOSE );
			if ( !$this->_isEnabled() )
			{
				$this->showMessage( "The replacement page is disabled. Enabling it. Current load avg {$current_load}" );
				if ( $this->_enablePage() ) // Cambia la pagina (softlink)
				{
					$this->showMessage( "Enabled ok." );
				}
				else
				{
					$this->showMessage( "CAUTION!, error enabling the replacement page. Validate write permissions in the root path." );
				}
			}
		}
		else
		{
			$this->subject_prefix = '[OK]';
			if ( ( $current_load + 0.25 ) <= $this->_load_limit && $this->_isEnabled() ) // Using a 0.25 marge error for avoid enable/disable loop.
			{
				$this->showMessage( "The load is going to be normal and the replacement page was enabled in a previous execution." );
				$this->showMessage( "Removing 'special' code. Current load avg {$current_load}" );
				if ( $this->_disablePage() ) // Cambia la pagina (softlink)
				{
					$this->showMessage( "The normal page enabled ok!. All right man :p" );
				}
				else
				{
					$this->showMessage( "CAUTION!, error disabling the replacement page." );
				}
			}

			$this->showMessage( "Finishing!", self::VERBOSE );
		}
	}
	 /**
         * Returns the subject of the email.
         */
        protected function getSubject()
        {
                return $this->subject_prefix . ' ' . parent::getSubject();
        }

}
?>
