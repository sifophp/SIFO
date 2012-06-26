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
		$parent_instance_sql     = '';
		$parent_instance_sub_sql = '';
		if ( $parent_instance )
		{
			$parent_instance_sql     = ' OR m.instance IS NULL ';
			$parent_instance_sub_sql = ' OR t.instance IS NULL ';
		}

		$sql = <<<TRANSLATIONS
SELECT
	*
FROM
	`i18n_messages` m
LEFT JOIN
	i18n_translations t ON m.id=t.id_message AND lang = ?
WHERE
	( m.instance = ? $parent_instance_sql ) AND
	( t.instance = ? $parent_instance_sub_sql )
ORDER BY
	t.translation ASC, m.message ASC
TRANSLATIONS;

		return $this->GetArray( $sql, array(
		                                   $language,
		                                   $instance,
		                                   $instance,
		                                   'tag' => 'Get all translations for current language'
		                              ) );
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
		$parent_instance_sql     = '';
		$parent_instance_sub_sql = '';
		if ( $parent_instance )
		{
			$parent_instance_sql     = ' OR m.instance IS NULL ';
			$parent_instance_sub_sql = ' OR t.instance IS NULL ';
		}

		$sql = <<<TRANSLATIONS
SELECT
	l.english_name,
	l.lang,
	lc.local_name AS name,
	l.lang AS lang,
	COUNT(m.id) AS total,
	COUNT(t.id_message) AS total_translated,
	ROUND( ( COUNT(t.id_message) / COUNT(m.id)) * 100, 2 ) AS percent,
	( COUNT(m.id) - COUNT(t.id_message) ) AS missing
FROM
	i18n_languages l
	LEFT JOIN i18n_language_codes lc ON l.lang = lc.l10n
	LEFT JOIN i18n_messages m ON ( m.instance = ? $parent_instance_sql )
	LEFT JOIN i18n_translations t ON m.id = t.id_message AND t.lang = l.lang AND ( t.instance = ? $parent_instance_sub_sql )
GROUP BY
	l.lang
ORDER BY
	percent DESC, english_name ASC
TRANSLATIONS;

		return $this->GetArray( $sql, array(
		                                   'tag' => 'Get current stats',
		                                   $instance,
		                                   $instance,
		                                   $instance,
		                                   $instance,
		                                   $instance
		                              ) );
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
	i18n_messages
SET
	message 	= ?,
	instance	= ?
SQL;

		return $this->Execute( $sql, array(
		                                  'tag' => 'Add message',
		                                  $message,
		                                  $instance
		                             ) );
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
	i18n_translations
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

		return $this->Execute( $sql, array(
		                                  'tag' => 'Add message',
		                                  $id_message,
		                                  $instance
		                             ) );
	}

	public function getTranslation( $message, $id_message = null )
	{
		$sql = <<<TRANSLATIONS
SELECT
	id
FROM
	i18n_messages
WHERE
	message = ? OR
	id 		= ?
TRANSLATIONS;

		return $this->getOne( $sql, array(
		                                 'tag' => 'Get correct id message',
		                                 $message,
		                                 $id_message
		                            ) );
	}

	public function getMessageInInhertitance( $message, $instance_inheritance )
	{
		if ( !empty( $instance_inheritance ) )
		{
			foreach ( $instance_inheritance as $instance )
			{
				if ( $instance != 'common' )
				{
					$instances[] = "'$instance'";
				}
			}
			$instance_inheritance = implode( ', ', $instances );
		}
		else
		{
			// Is an instance parent.
			return 0;
		}

		$sql = <<<SQL
SELECT
	COUNT(*)
FROM
	i18n_messages
WHERE
	message = ? AND
	( instance IN ( $instance_inheritance ) OR instance IS NULL )
SQL;

		return $this->getOne( $sql, array(
		                                 'tag' => 'Get message in inheritance',
		                                 $message
		                            ) );
	}
}