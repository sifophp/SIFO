<?php

class I18nRebuildController extends Controller
{
	public $is_json = true;

	public function build()
	{

		$translator = $this->getClass( 'I18nTranslatorModel' );
		$filter = Filter::getInstance();

		$given_translation = $filter->getString( 'translation' );
		$id_message = $filter->getString( 'id_message' );

		$langs_in_DB = $translator->getDifferentLanguages();

		foreach ( $langs_in_DB as $l )
		{
			$language_list[] = $l['l10n'];
		}

		foreach ( $language_list as $language )
		{
			$language_str = $translator->getTranslations( $language );

			foreach ( $language_str as $str )
			{
				$msgid = $str['message'];
				$msgstr = ( $str['translation'] == null ? '' : $str['translation'] );
				$translations[$msgid][$language] = $msgstr;
			}
			unset( $language_str );
		}

		ksort( $translations );
		$failed = array();
		$result = true;

		foreach ( $language_list as $language )
		{
			$buffer ='';
			$empty_strings_buffer ='';
			$empty[$language] = 0;
			foreach ( $translations as $msgid => $msgstr )
			{

				$item = '$translations["'. str_replace( '"', '\"', $msgid ) .'"] = ' . '"';
				if ( $language != 'en_US' )
				{
					$item .= str_replace( '"', '\"', $msgstr[$language] );
				}
				else
				{
					$item .= str_replace( '"', '\"', $msgid );
				}

				$item .= "\";\n";
				if ( $msgstr[$language] == '' )
				{
					$empty[$language]++;
					$empty_strings_buffer .= $item;
				}
				else
				{
					$buffer .= $item;
				}
			}
			$buffer = "<?php\n// Translations file, lang='$language'\n// Empty strings: $empty[$language]\n$empty_strings_buffer\n// Completed strings:\n$buffer\n?>";
			$path = ROOT_PATH . '/instances/' . Bootstrap::$instance . '/locale/messages_' .$language .'.php';
			$write = @file_put_contents( $path, $buffer );

			if ( !$write )
			{
				$failed[] = $language;
			}

			$result = $result && $write;
		}

		if ( $result )
		{
			return array(
					'status' => 'OK',
					'msg' => 'Successfully saved'
					);
		}


		return array(
			'status' => 'KO',
			'msg' => 'Failed to save the translation:' . implode( "\n", $failed )
		);
	}
}