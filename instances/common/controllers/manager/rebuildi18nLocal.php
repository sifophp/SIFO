<?php

namespace Common;
/**
 * A rebuild for languages when no database is used.
 *
 * Parses all the controllers and templates and adds the missing translations to the array. No deletion is done.
 * If you want to delete a string must be deleted from every locale.
 */
class ManagerRebuildi18nLocalController extends \Sifo\Controller
{
	const MASTER_LANGUAGE = 'en_US';

	public function indexAction()
	{
		$this->setLayout( 'manager/findi18n.tpl' );

		$findI18N = new ManagerFindi18nController();


		$locales_available = $findI18N->getFilesystemFiles( "instances/{$this->instance}/locale" );
		foreach ( $locales_available as $key => $locale )
		{
			if ( false === strpos( $locale, 'messages_' ) )
			{
				unset( $locales_available[$key] ); // Rebuild only "messages".
			}
		}

		$literals = $findI18N->getLiterals( $this->instance );

		// Look for master file and save contents from literals:
		foreach ( $locales_available as $key => $locale )
		{
			if ( strpos( $locale, self::MASTER_LANGUAGE ) !== false )
			{
				$master_strings = $this->getTranslationStrings( $locale, $literals );
				unset( $locales_available[$key] );
				$this->saveStrings( $locale, $master_strings );
			}
		}

		$master_keys = array_keys( $master_strings );

		foreach ( $locales_available as $key => $locale )
		{
			$translated_strings = $this->getTranslationStrings( $locale, $literals );

			// Remove keys not present in master:
			foreach( $translated_strings as $key_translated => $value_translated )
			{
				if ( !in_array( $key_translated, $master_keys ) )
				{
					unset( $translated_strings[$key_translated] );
				}
			}

			$this->saveStrings($locale, $translated_strings);
		}

		die( "Locales rebuilt" );
	}

	protected function saveStrings( $locale, $translations )
	{
		$translations_file = \Sifo\Bootstrap::$application . "/{$this->instance}/locale/$locale";
		file_put_contents( $translations_file, "<?php

namespace Common;\n"
				. '$translations = ' . var_export( $translations, true ) . ';' );

	}

	protected function getTranslationStrings( $locale, $literals )
	{
		$path = \Sifo\Bootstrap::$application . "/{$this->instance}";
		$translations_file = "$path/locale/$locale";

		echo "<h1>Rebuild result for $locale</h1>";
		echo "<pre>";
		if ( file_exists( $translations_file ) )
		{
			include "$translations_file";

			$missing = array();

			foreach( $literals as $key => $template )
			{
				if ( !isset( $translations[$key] ) )
				{
					$missing[$key] = '';
				}
				else
				{
					if ( empty( $translations[$key] ) )
					{
						$missing[$key] = '';
						unset( $translations[$key] );
					}
				}
				echo "\nLiteral:\t\"<strong>$key</strong>\" => \"<em>$translations[$key]</em>\"\nFound in:\t$template\n";
			}
			echo "</pre>";

			ksort( $translations );
			ksort( $missing );

			$translations = array_merge( $missing,$translations );


			return $translations;

		}
		else
		{
			die( "File $locale not available for <strong>$instance</strong>" );
		}
	}
}