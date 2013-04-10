<?php

namespace Common;

class I18nActionsController extends \Sifo\Controller
{
	public $is_json = true;

	public function indexAction()
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

		return $result;
	}

	/**
	 * Add message.
	 * @return mixed
	 */
	protected function addMessage( $instance )
	{
		$message 			= \Sifo\FilterPost::getInstance()->getString( 'msgid' );
		$translator_model 	= new I18nTranslatorModel();

		// Check if this message exists in the instances parents.
		// Get selected instance inheritance.
		$instance_domains 		= $this->getConfig( 'domains', $instance );

		$instance_inheritance = array();
		if ( isset( $instance_domains['instance_inheritance'] ) )
		{
			$instance_inheritance 	=  $instance_domains['instance_inheritance'];
		}

		if ( $translator_model->getMessageInInhertitance( $message, $instance_inheritance ) > 0 )
		{
			return array( 'status' => 'KO', 'msg' => 'This message already exists in parent instance. Please, customize it.' );
		}
		elseif ( $translator_model->addMessage( $message, $instance ) )
		{
			return array( 'status' => 'OK', 'msg' => 'Message successfully saved.' );
		}

		return array( 'status' => 'KO', 'msg' => 'Failed adding message.' );
	}

	/**
	 * Customize translation.
	 * @return mixed
	 */
	protected function customizeTranslation()
	{
		$message 		= \Sifo\FilterPost::getInstance()->getString( 'msgid' );
		$id_message 	= null;
		if ( is_numeric( $message ) )
		{
			$id_message = $message;
		}
		$instance			= $this->getParsedParam( 'instance' );
		$translator_model 	= new I18nTranslatorModel();

		$id_message = $translator_model->getTranslation( $message, $id_message );

		$result = array( 'status' => 'KO', 'msg' => 'This Message or ID doesn\'t exist.' );
		if ( $id_message )
		{
			$result = array( 'status' => 'OK', 'msg' => 'Message successfully customized.' );
			if( !$translator_model->customizeTranslation( $id_message, $instance ) )
			{
				$result = array( 'status' => 'KO', 'msg' => 'This message is already customized in this instance.' );
			}
		}

		return $result;
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