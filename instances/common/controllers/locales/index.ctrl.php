<?php
namespace Common;

namespace Common;

/**
 * Root locale controller
 * @author edufabra
 */
class LocalesIndexController extends \Sifo\Controller
{
	protected $include_classes = array( 'FlashMessages' );

	/**
	 * Main method controller.
	 * @return unknown_type
	 */
	public function build()
	{
		$this->setLayout( 'locales/index.tpl' );
		$this->addModule( 'head', 'SharedHead' );
		$this->addModule( 'footer', 'SharedFooter' );
		$this->addModule( 'system_messages', 'SharedSystemMessages' );
		
		$params = $this->getParams();
		$action = \Sifo\Router::getReversalRoute( $params['path_parts'][0] );

		$params = $this->getParams();

		
		if ( $params['params'] !== false && in_array('saved-true', $params["params"]))
		{
			\Sifo\FlashMessages::set( 'File Saved OK.', \Sifo\FlashMessages::MSG_OK );
		}
		if ( $params['params'] !== false && in_array('created-true', $params["params"]))
		{
			\Sifo\FlashMessages::set( 'File Created OK.', \Sifo\FlashMessages::MSG_OK );
		}


		if ($this->getParsedParam( 'instance' ) !== false)
		{
			$this->assign('current_instance', $this->getParsedParam( 'instance' ));
			$this->assign('languages', $this->getLanguagesInstance($this->getParsedParam( 'instance' )));
			//var_dump($this->getLanguagesInstance($this->getParsedParam( 'instance' )));
		}
		if ($this->getParsedParam( 'language' ) !== false)
		{
			$this->assign('current_language', $this->getParsedParam( 'language' ));
			$temp = explode("_", $this->getParsedParam( 'language' ));
			if (  is_array( $temp))
			{
				$this->assign('lang', $temp[1]);
			}
		}

		$instances = $this->getInstances();
		$this->assign('instances', $instances);
		// For url with params generation:
		$this->assign( 'params', $this->getParam( 'parsed_params' ) );
		$this->assign( 'params_definition', $this->getParamsDefinition() );
	}

	/**
	 * Build a list with all of the instances
	 * @return array
	 */
	public function getInstances()
	{
		$instances = $this->scanFiles(ROOT_PATH . '/instances');
		return $instances;
	}

	/**
	 * Build a list with all of the languages of an instance
	 * @return array
	 */
	public function getLanguagesInstance($instance = null)
	{
		$languages = false;
		if ($instance != '')
		{
			$languages = $this->scanFiles(ROOT_PATH . '/instances/' . $instance . '/locale', false);
			if (is_array($languages) && count($languages) > 0)
			{
				$locales = array();
				foreach ($languages as $index => $language)
				{
					require_once ROOT_PATH . '/instances/' . $instance . '/locale/' . $language["id"];
					if (isset($translations))
					{
						$locales[$language["id"]]["translations"] = $translations;
						unset($translations);
					}
					else
					{
						$locales[$language["id"]]["translations"] = null;
					}
				}

				$translation_keys = $this->getUniqueTranslationKeys($locales);
				if (is_array($translation_keys)) natcasesort($translation_keys);
				$this->assign("translation_keys", $translation_keys);
				if (is_array($translation_keys))
				{
					$total_keys = count($translation_keys);
					foreach($locales as $language => $value)
					{
						$locales[$language]["total_keys"]				= $total_keys;
						$locales[$language]["translated_keys"]			= count($locales[$language]["translations"]);
						$locales[$language]["translated_percentage"]	= number_format(($locales[$language]["translated_keys"] / $locales[$language]["total_keys"]) * 100, 2, ",", ".")."%";
					}

				}
			}
		}
		return($locales);
	}

	private function scanFiles($path, $only_folders = true)
	{
		$files = false;
		if ($handle = opendir($path))
		{
			while (false !== ($file = readdir($handle)))
			{
				if ($only_folders)
				{
					if (is_dir($path . "/" . $file . "/") && strpos( $file, "." ) === false)
					{
						$files[] = array('id' => $file, 'name' => $file);
					}
				}
				else if ( 0 !== strpos( $file, '.' ) )
				{
					$files[] = array('id' => $file, 'name' => $file);
				}
			}
			closedir($handle);
		}
		return($files);
	}

	private function getUniqueTranslationKeys($locales = null)
	{
		$keys = false;
		if ( is_array( $locales) )
		{
			$translation_keys = array();
			foreach ( $locales as $language_file => $keys )
			{
				if (is_array($keys) && isset($keys["translations"]))
				{
					foreach($keys["translations"] as $key => $translation)
					{
						if (!in_array($key, $translation_keys))
						{
							$translation_keys[] = $key;
						}
					}
				}
			}
		}

		return $translation_keys;
	}

	protected function getParamsDefinition()
	{
		return array(
			'instance' => array(
				'internal_key' => 'i',
				'is_list' => false,
				'apply_translation' => false,
			),
			'language' => array(
				'internal_key' => 'l',
				'is_list' => false,
				'apply_translation' => false,
			)
		);
	}
}
