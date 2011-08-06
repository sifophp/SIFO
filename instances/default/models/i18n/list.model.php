<?php

class I18nListModel extends Model
{
	public function getAvailableLangs()
	{
		$key = get_class( $this );
		if ( $languages = $this->inRegistry( $key ) )
		{
			return $languages;
		}

		// In debug all languages are shown:
		if ( Domains::getInstance()->getDevMode() )
		{
				$sql = <<<TRANSLATIONS
SELECT
	*
FROM
	`i18n_language_codes`
WHERE l10n IS NOT NULL
ORDER BY
	local_name ASC
TRANSLATIONS;
		}
		else // Production
		{
			$sql = <<<TRANSLATIONS
SELECT
	*
FROM
	`i18n_language_codes`
WHERE enabled = 'Yes' AND l10n IS NOT NULL
ORDER BY
	local_name ASC
TRANSLATIONS;
		}

	$languages = $this->GetArray( $sql, array( 'tag' => 'Get list of available languages' ) );
	$this->storeInRegistry( $key, $languages );
	return $languages;

	}
}