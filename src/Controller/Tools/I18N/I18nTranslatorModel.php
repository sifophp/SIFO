<?php


namespace Sifo\Controller\Tools\I18N;

use Sifo\Database\Model;

class I18nTranslatorModel extends Model
{
    /**
     * Returns the translations list for a given language.
     *
     * @param string $language
     * @param $instance
     * @param bool $parent_instance
     *
     * @return array|bool
     */
    public function getTranslations($language, $instance, $parent_instance = false)
    {
        $filter_sql = 'm.instance = ? OR t.instance = ?';
        if ($parent_instance) {
            $filter_sql = '( m.instance = ? OR m.instance IS NULL ) AND ( t.instance = ? OR t.instance IS NULL )';
        }

        $sql = <<<SQL
SELECT
	*
FROM
	`i18n_messages` m
LEFT JOIN
	i18n_translations t ON m.id=t.id_message AND lang = ?
WHERE
    $filter_sql
ORDER BY
	IF(t.id_message IS NULL,0,1),LOWER(CONVERT(m.message USING utf8)),m.id
SQL;

        return $this->GetAll($sql, [
            $language,
            $instance,
            $instance,
            'tag' => 'Get all translations for current language'
        ]);
    }

    /**
     * List of different languages found in DB.
     *
     * @return array|bool
     */
    public function getDifferentLanguages()
    {
        $sql = <<<SQL
SELECT
	*
FROM
	`i18n_language_codes`
WHERE l10n IS NOT NULL
ORDER BY
	english_name ASC
SQL;

        return $this->GetAll($sql, ['tag' => 'List of different languages in DB']);
    }

    /**
     * Get stats of translations.
     *
     * @param string $instance
     * @param string $parent_instance
     *
     * @return array|bool
     */
    public function getStats($instance, $parent_instance)
    {
        $parent_instance_sql = '';
        $parent_instance_sub_sql = 'm.instance = ? OR t.instance = ?';
        if ($parent_instance) {
            $parent_instance_sql = ' OR instance IS NULL ';
            $parent_instance_sub_sql = '( m.instance = ? OR m.instance IS NULL ) AND ( t.instance = ? OR t.instance IS NULL )';
        }

        $sql = <<<SQL
SELECT
	l.english_name,
	l.lang,
	lc.local_name AS name,
	@lang 			:= l.lang AS lang,
	@translated 	:= (SELECT COUNT(*) FROM i18n_translations WHERE ( instance = ? $parent_instance_sql ) AND lang = @lang AND translation != '' AND translation IS NOT NULL ) AS total_translated,
	@total 			:=  (SELECT COUNT(DISTINCT(m.id)) FROM i18n_messages m LEFT JOIN i18n_translations t ON m.id=t.id_message AND t.lang = @lang WHERE $parent_instance_sub_sql ) AS total,
	ROUND( ( @translated / @total) * 100, 2 ) AS percent,
	( @total - @translated ) AS missing
FROM
	i18n_languages l
	LEFT JOIN i18n_language_codes lc ON l.lang = lc.l10n
ORDER BY
	percent DESC, english_name ASC
SQL;

        return $this->GetAll($sql, [
            'tag' => 'Get current stats',
            $instance,
            $instance,
            $instance,
            $instance,
            $instance
        ]);
    }

    /**
     * Add message in database.
     *
     * @param string $message
     * @param null|string $instance
     *
     * @return mixed
     */
    public function addMessage($message, $instance = null)
    {
        $sql = <<<SQL
INSERT INTO
	i18n_messages
SET
	message 	= ?,
	instance	= ?
SQL;

        return $this->Execute($sql, [
            'tag' => 'Add message',
            $message,
            $instance
        ]);
    }

    /**
     * Add translations for one message in an specific instance.
     *
     * @param $id_message
     * @param $instance
     *
     * @return mixed
     */
    public function customizeTranslation($id_message, $instance)
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

        return $this->Execute($sql, [
            'tag' => 'Add message',
            $id_message,
            $instance
        ]);
    }

    /**
     * @param $message
     * @param null $id_message
     *
     * @return bool|string
     */
    public function getTranslation($message, $id_message = null)
    {
        $sql = <<<SQL
SELECT
	id
FROM
	i18n_messages
WHERE
	message = ? OR
	id 		= ?
SQL;

        return $this->GetOne($sql, [
            'tag' => 'Get correct id message',
            $message,
            $id_message
        ]);
    }

    public function getMessageInInheritance($message, $instance_inheritance)
    {
        if (empty($instance_inheritance)) {
            return 0;
        }

        $instances = array();
        foreach ($instance_inheritance as $instance) {
            if ($instance !== 'common') {
                $instances[] = "'$instance'";
            }
        }
        $instance_inheritance = implode(', ', $instances);

        $sql = <<<SQL
SELECT
	COUNT(*)
FROM
	i18n_messages
WHERE
	message = ? AND
	( instance IN ( $instance_inheritance ) OR instance IS NULL )
SQL;

        return $this->GetOne($sql, [
            'tag' => 'Get message in inheritance',
            $message
        ]);
    }
}
