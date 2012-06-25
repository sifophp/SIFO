<?php

namespace Common;

/**
 * Manages the translations.
 */
class I18nTranslatorModel extends \Sifo\Model
{
	/**
	 * Returns the translations list for a given langauge.
	 *
	 * @param string $language
	 * @return array
	 */
	public function getTranslations( $language, $instance, $parent_instance = false )
	{
		$parent_instance_sql 		= '';
		if ( $parent_instance )
		{
			$parent_instance_sql 		= ' OR t.instance IS NULL ';
		}

		$sql = <<<TRANSLATIONS
SELECT
	*
FROM
	`i18n_messages_copy` m
LEFT JOIN
	i18n_translations_copy t ON m.id=t.id_message AND lang = ?
WHERE
	( m.instance = ? OR t.instance = ? $parent_instance_sql )
ORDER BY
	t.translation ASC, m.message ASC
TRANSLATIONS;

	return $this->GetArray( $sql, array( $language, 'tag' => 'Get all translations for current language', $instance, $instance ) );
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
	public function getStats( $instance, $parent_instance )
	{
		$parent_instance_sql 		= '';
		$parent_instance_sub_sql	= '';
		if ( $parent_instance )
		{
			$parent_instance_sql 		= ' OR instance IS NULL ';
			$parent_instance_sub_sql 	= ' OR m.instance IS NULL ';
		}

		$sql = <<<TRANSLATIONS
SELECT
	t.lang,
	l.local_name as name,
	l.english_name,
	ROUND( ( count(*) / (SELECT COUNT( DISTINCT(m.id) ) FROM i18n_messages_copy m LEFT JOIN i18n_translations_copy tc2 ON m.id = tc2.id_message WHERE ( m.instance = ? OR tc2.instance = ?  $parent_instance_sub_sql  ) ) * 100 ), 2 ) as percent,
	( ( SELECT COUNT( DISTINCT(m.id) ) FROM i18n_messages_copy m LEFT JOIN i18n_translations_copy tc2 ON m.id = tc2.id_message WHERE ( m.instance = ? OR tc2.instance = ?  $parent_instance_sub_sql  ) ) - COUNT(*) ) AS missing
FROM
	i18n_translations_copy t
	INNER JOIN i18n_language_codes l ON t.lang = l.l10n
WHERE
	instance = ?
	$parent_instance_sql
GROUP BY
	t.lang
ORDER BY
	percent DESC, english_name ASC
TRANSLATIONS;

	return $this->GetArray( $sql, array( 'tag' => 'Get current stats', $instance, $instance, $instance, $instance, $instance ) );
	}

	/**
	 * Add message in database.
	 * @param $message
	 * @return mixed
	 */
	public function addMessage( $message, $instance = null )
	{
		$sql = <<<SQL
INSERT INTO
	i18n_messages_copy
SET
	message 	= ?,
	instance	= ?
SQL;

		return $this->Execute( $sql, array( 'tag' => 'Add message', $message, $instance ) );
	}

	/**
	 * Add translations for one message in an specific instance.
	 * @param $message
	 * @param $instance
	 * @return mixed
	 */
	public function customizeTranslation( $id_message, $instance )
	{
		$sql = <<<SQL
INSERT INTO
	i18n_translations_copy
SELECT
	?,
	lang,
	'',
	'customize',
	NOW(),
	?
FROM
	i18n_languages;
SQL;

		return $this->Execute( $sql, array( 'tag' => 'Add message', $id_message, $instance ) );
	}

	public function getTranslation( $message, $id_message = null )
	{
		$sql = <<<TRANSLATIONS
SELECT
	id
FROM
	i18n_messages_copy
WHERE
	message = ? OR
	id 		= ?
TRANSLATIONS;

		return $this->getOne( $sql, array( 'tag' => 'Get correct id message', $message, $id_message ) );
	}
}