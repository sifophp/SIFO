<?php

namespace Common;

class I18nActionsController extends \Sifo\Controller
{
	public $is_json = true;

	public function build()
	{
		if ( !\Sifo\Domains::getInstance()->getDevMode() )
		{
			throw new \Sifo\Exception_404( 'Translation only available while in devel mode' );
		}

		// Get instance name.
		$params 	= $this->getParams();
		switch( $params['parsed_params']['action'] )
		{
			case 'addMessage':
			{
				$result = $this->addMessage( $params['parsed_params']['instance'] );
			}
			break;
			case 'customizeTranslation':
			{
				$result = $this->customizeTranslation();
			}
			break;
		}

		return array(
			'status' => ( $result ) ? 'OK' : 'KO',
			'msg' => ( $result ) ? 'Successfully saved' : 'Failed ' . $params['parsed_params']['action'],
		);
	}

	/**
	 * Add message.
	 * @return mixed
	 */
	protected function addMessage( $instance )
	{
		$message 	= \Sifo\FilterPost::getInstance()->getString( 'msgid' );

		$translator_model = new I18nTranslatorModel();
		return $translator_model->addMessage( $message, $instance );
	}

	/**
	 * Customize translation.
	 * @return mixed
	 */
	protected function customizeTranslation()
	{
		$message 		= \Sifo\FilterPost::getInstance()->getString( 'msgid' );
		if ( is_numeric( $message ) )
		{
			$id_message = $message;
		}
		$instance			= $this->getParsedParam( 'instance' );
		$translator_model 	= new I18nTranslatorModel();

		$id_message = $translator_model->getTranslation( $message, $id_message );

		if ( $id_message )
		{
			return $translator_model->customizeTranslation( $message, $instance );
		}

		return false;
	}

	/**
	 * Define accepted params for this controller.
	 *
	 * @return array
	 */
	protected function getParamsDefinition()
	{
		$config = array(
					'action' => array(
						'internal_key' => 'a',
						'is_list' => false,
						'apply_translation' => false
					),
					'instance' => array(
						'internal_key' => 'i',
						'is_list' => false,
						'apply_translation' => false
					),
				);
		return $config;
	}
}