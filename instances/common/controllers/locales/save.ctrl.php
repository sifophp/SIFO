<?php

/**
 * Save translations into files.
 * @author edufabra
 */
class localesSaveController extends Controller
{

	/**
	 * Main of controller.
	 * @return unknown_type
	 */
	public function build()
	{
		$this->setLayout( 'locales/index.tpl' );
		$this->addModule( 'head', 'SharedHead' );
		$this->addModule( 'footer', 'SharedFooter' );
		$params = $this->getParams();
		$action = Router::getReversalRoute( $params['path_parts'][0] );
		switch ( $action )
		{
			case 'locales-save':
				$this->save();
				break;

			default:
				break;
		}
		$this->assign( 'params', $this->getParam( 'parsed_params' ) );
		$this->assign( 'params_definition', $this->getParamsDefinition() );
	}

	/**
	 * Save translations into file
	 *
	 * @return void
	 */
	public function save()
	{
		$post = FilterPost::getInstance();
		if ( $post->isSent( 'save' ) )
		{
			$translations = $post->getArray( "translations" );
			//if ( is_array( $translations ) ) sort($translations);
			$instance = $post->getString('instance');
			$language_file = $post->getString('language');

			foreach($translations as $index => $translation)
			{
				if ($translation["translation"] != '')
				{
					$translated_keys[] = $translation;
				}
			}
			if (is_array($translated_keys))
			{
				$this->natksort($translated_keys);
				$file = fopen(ROOT_PATH . '/instances/' . $instance . '/' . 'locale/' . $language_file, 'w+');
				fwrite($file, '<?php'."\n");
				foreach($translated_keys as $index => $translation)
				{
					if ($translation["translation"] != '')
					{
						if ( get_magic_quotes_gpc() )
						{
							$text = '$translations["'.str_replace("\'", "'", $translation["key"]).'"] = "'.str_replace("\'", "'", $translation["translation"]).'";'."\n";
						}
						else
						{
							$text = '$translations["'.str_replace('"', '\"', $translation["key"]).'"] = "'.str_replace('"', '\"', $translation["translation"]).'";'."\n";
						}
						fwrite($file, $text);
					}
				}
				fwrite($file, '?>');
				fclose($file);
			}
			$params = $this->getParams();
			throw new Exception_302( $params['url']['locales'] . ':saved-true:i:'.$instance.':l:'.$language_file );
		}
		else
		{
			$params = $this->getParams();
			if (isset($params) && isset($params["parsed_params"]["instance"]) && isset($params["parsed_params"]["new_language"]))
			{
				$file = fopen(ROOT_PATH . '/instances/' . $params["parsed_params"]["instance"] . '/' . 'locale/' . $params["parsed_params"]["new_language"], 'w+');
				if ($file)
				{
					fwrite($file, '<?php'."\n");
					fclose($file);
				}
				throw new Exception_302( $params['url']['locales'] . ':created-true:i:'.$params["parsed_params"]["instance"] );
			}
		}
	}

	private function natksort($array)
	{
		$original_keys_arr = array();
		$original_values_arr = array();
		$clean_keys_arr = array();

		$i = 0;
		foreach ($array AS $key => $value)
		{
			$original_keys_arr[$i] = $key;
			$original_values_arr[$i] = $value;
			$clean_keys_arr[$i] = strtr($key, "ÄÖÜäöüÉÈÀËëéèàç", "AOUaouEEAEeeeac");
			$i++;
		}

		natcasesort($clean_keys_arr);

		$result_arr = array();

		foreach ($clean_keys_arr AS $key => $value)
		{
			$original_key = $original_keys_arr[$key];
			$original_value = $original_values_arr[$key];
			$result_arr[$original_key] = $original_value;
		}

		return $result_arr;
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
			),
			'new_language' => array(
				'internal_key' => 'n',
				'is_list' => false,
				'apply_translation' => false,
			)
		);
	}
}