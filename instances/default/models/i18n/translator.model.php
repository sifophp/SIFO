<?php

/**
 * Manages the translations.
 */
class I18nTranslatorModel extends Model
{
	/**
	 * Returns the translations list for a given langauge.
	 *
	 * @param string $language
	 * @return array
	 */
	public function getTranslations( $language )
	{
		$sql = <<<TRANSLATIONS
SELECT
	*
FROM
	`i18n_messages` m
LEFT JOIN
	i18n_translations t ON m.id=t.id_message AND lang =?
ORDER BY
	t.translation ASC, m.message ASC
TRANSLATIONS;

	return $this->GetArray( $sql, array( $language, 'tag' => 'Get all translations for current language' ) );
	}

	/**
	 * List of differens languages found in DB.
	 *
	 * @return unknown
	 */
	public function getDifferentLanguages()
	{
		$sql = <<<TRANSLATIONS
SELECT
	*
FROM
	`i18n_language_codes`
WHERE l10n IS NOT NULL
ORDER BY
	english_name ASC
TRANSLATIONS;

	return $this->GetArray( $sql, array( 'tag' => 'List of different languages in DB' ) );
	}

	/**
	 * Get stats of translations.
	 *
	 * @return array
	 */
	public function getStats()
	{
		$sql = <<<TRANSLATIONS
SELECT
	t.lang,
	l.local_name as name,
	l.english_name,
	ROUND( ( count(*)/(SELECT count(*) FROM i18n_messages m) * 100 ), 2 ) as percent,
	(SELECT count(*) FROM i18n_messages m) - count(*) as missing
FROM
	i18n_translations t
INNER JOIN
	i18n_language_codes l ON t.lang = l.l10n
GROUP BY
	t.lang
ORDER BY
	percent DESC, english_name ASC
TRANSLATIONS;

	return $this->GetArray( $sql, array( 'tag' => 'Get current stats' ) );
	}
}