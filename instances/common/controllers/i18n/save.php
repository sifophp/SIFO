<?php

namespace Common;

class I18nSaveController extends \Sifo\Controller
{
	public $is_json = true;

	public function build()
	{
		if ( !\Sifo\Domains::getInstance()->getDevMode() )
		{
			throw new \Sifo\Exception_404( 'Translation only available while in devel mode' );
		}

		$filter = \Sifo\Filter::getInstance();

		// Get instance name.
		$params 	= $this->getParams();
		$instance 	= $this->instance;

		if ( isset( $params['params'][0] ) )
		{
			$instance = $params['params'][0];
		}

		$lang = $filter->getString('lang');
		$given_translation = $filter->getUnfiltered( 'translation' );
		$id_message = $filter->getString( 'id_message' );
		$translator_email = ( !isset( $user['email'] ) ) ? '' : $user['email'];

		if ($given_translation )
		{
			// TODO: REMOVE this: Temporal fix until magic quotes is disabled:
			$given_translation = str_replace( '\\', '', $given_translation );

			$query = 'REPLACE i18n_translations (id_message, lang, translation,author,instance) VALUES(?,?,?,?,?);';

			$result = \Sifo\Database::getInstance()->Execute( $query, array( $id_message, $lang, $given_translation, $translator_email, $instance ) );

			if ( $result )
			{
				return array(
					'status' => 'OK',
					'msg' => 'Successfully saved'
				);
			}
		}

		return array(
			'status' => 'KO',
			'msg' => 'Failed to save the translation'
		);
	}
}