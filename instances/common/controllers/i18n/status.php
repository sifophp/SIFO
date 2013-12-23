<?php
namespace Common;

class I18nStatusController extends \Sifo\Controller
{
	public function build()
	{
		if ( !\Sifo\Domains::getInstance()->getDevMode() )
		{
			throw new \Sifo\Exception_404( 'Translation only available while in devel mode' );
		}

		$this->addModule( 'system_messages', 'SharedSystemMessages' );

		$this->setLayout( 'i18n/status.tpl' );

		// Instance navigation.
		$current_instance_inheritance = \Sifo\Domains::getInstance()->getInstanceInheritance();
		$this->assign( 'current_instance_inheritance', $current_instance_inheritance );


		// Get instance name.
		$params 	= $this->getParams();
		$instance 	= $this->instance;

		if ( isset( $params['params'][0] ) )
		{
			$instance = $params['params'][0];
 		}

		// Get selected instance inheritance.
		$instance_domains 		= $this->getConfig( 'domains', $instance );

		$instance_inheritance = array();
		if ( isset( $instance_domains['instance_inheritance'] ) )
		{
			$instance_inheritance 	=  $instance_domains['instance_inheritance'];
		}

		$is_parent_instance = false;
		if ( empty( $instance_inheritance ) || ( count( $instance_inheritance ) == 1 && $instance_inheritance[0] == 'common' )  )
		{
			$is_parent_instance = true;
		}

		$translator 			= new I18nTranslatorModel();
		$different_languages 	= $translator->getStats( $instance, $is_parent_instance );
		$current_lang			= $this->getCurrentLang();
		$translations 			= false;

		$this->assign( 'langs', $different_languages );

		// The languages are defined with 5 chars. E.g: es_ES
		if ( $current_lang && 5 == strlen( $current_lang ) )
		{
			$translations = $translator->getTranslations( $current_lang, $instance, $is_parent_instance );
		}

		$this->assign( 'instance', $instance );
		$this->assign( 'instance_inheritance', $instance_inheritance );
		$this->assign( 'is_parent_instance', $is_parent_instance );
		$this->assign( 'different_languages', $different_languages );
		$this->assign( 'translations', $translations );
		$this->assign( 'curr_lang', $current_lang );
		$this->assign( 'can_edit', $this->canEdit() );
		$this->assign( 'isAdmin', $this->isAdmin() );

	}

	/**
	 * Returns the language requested to translate.
	 *
	 * This method might change depending on the definition of your URLs.
	 */
	protected function getCurrentLang()
	{
		return $this->getUrlParam( 1 );
	}

	protected function canEdit()
	{
		return true;
	}

	protected function isAdmin()
	{
		return true;
	}

	public function getCacheDefinition()
	{
		// No language passed
		if ( !$this->getUrlParam( 0 ) )
		{
			return array( 'name' => 'translation_status', 'expiration' => 60 ); // 1 minute
		}

		return false;
	}

}