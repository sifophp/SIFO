<?php

namespace Common;

include_once ROOT_PATH . '/instances/common/controllers/shared/commandLine.php';

class ScriptsFlushCacheController extends SharedCommandLineController
{
	private $_last_accessed;
	private $_last_modified;

	private function _flush()
	{
		$cache_controllers_path = ROOT_PATH . "/instances/" . $this->getParam( 'instance' ) . "/templates/_smarty/cache";
		$cache_templates_path = ROOT_PATH . "/instances/" . $this->getParam( 'instance' ) . "/templates/_smarty/compile";
		$command_options = '';
		if ( isset( $this->_last_accessed ) )
		{
			$command_options .= " -amin +" . $this->_last_accessed;
		}
		if ( isset( $this->_last_modified ) )
		{
			$command_options .= " -mmin +" . $this->_last_modified;
		}

		// Use the regex option for elude no cache files (like .svn).
		$command_controllers = "find $cache_controllers_path/ -regex '.+html$'" . $command_options;
		$command_templates = "find $cache_templates_path/ -regex '.+php$'" . $command_options;
		exec("$command_controllers 2>&1", $files_to_remove_controllers, $err);
		if ( $err )
		{
			$this->showMessage( "'$err' executing '$command_controllers'", self::VERBOSE );
		}

		exec("$command_templates 2>&1", $files_to_remove_templates, $err);
		if ( $err )
		{
			$this->showMessage( "'$err' executing '$command_templates'", self::VERBOSE );
		}
		$files_to_remove = array_merge( $files_to_remove_templates, $files_to_remove_controllers );
		$this->showMessage( "Will remove ".count( $files_to_remove )." files", self::ALL );
		$ok = 0;
		$ko = 0;
		foreach ( $files_to_remove as $file )
		{
			$this->showMessage( "Removing $file...", self::VERBOSE );
			if ( $this->test )
			{
				$this->showMessage( "Not removed in test mode.", self::VERBOSE );
			}
			else
			{
				if ( unlink( $file ) )
				{
					$this->showMessage( "Ok. Removed.", self::VERBOSE );
					$ok++;
				}
				else
				{
					$ko++;
					$this->showMessage( "Ko. Unaccesible.", self::VERBOSE );
				}
			}
		}
		$this->showMessage( "$ok files flushed succesfully and $ko with error. You can run in verbose mode for more details.", self::ALL );
	}


	// ABSTRACTED METHODES:

	public function init()
	{
		$this->help_str = 'Flush the disk cache.'.PHP_EOL;
		$this->help_str .= 'Can filter the flush by last access time. Caution!: Without options remove all the cache files.'.PHP_EOL;

		$this->setNewParam( 'a', 'amin', 'Flush files was not last accessed n minutes ago.', true, false );
		$this->setNewParam( 'm', 'mmin', 'Flush status was not last modified n minutes ago.', true, false );

	}

	public function exec()
	{
		$this->showMessage( "Starting the flush", self::VERBOSE );
		foreach ( $this->command_options as $option )
		{
			switch ( $option[0] )
			{
				case "a":
				case "amin":
					$this->_last_accessed = $option[1];
					$this->showMessage( "Will flush the files was not last accessed in " . $option[1] . "minutes.", self::VERBOSE );
					break;
				case "m":
				case "mmin":
					$this->_last_modified = $option[1];
					$this->showMessage( "Will flush the files was not last modified in " . $option[1] . "minutes.", self::VERBOSE );
					break;
			}
		}
		$this->_flush();

		$this->showMessage( "Finishing!", self::VERBOSE );
	}
}
?>