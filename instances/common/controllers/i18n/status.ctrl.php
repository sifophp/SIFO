<?php
namespace Common;

namespace Common;

class I18nStatusController extends \Sifo\Controller
{
	public function build()
	{
		if ( !\Sifo\Domains::getInstance()->getDevMode() )
		{
			throw new \SifoException_404( 'Translation only available while in devel mode' );
		}

		$this->addModule( 'head', 'SharedHead' );
		$this->addModule( 'header', 'SharedHeader' );
		$this->addModule( 'footer', 'SharedFooter' );
		$this->addModule( 'system_messages', 'SharedSystemMessages' );

		$this->setLayout( 'i18n/status.tpl' );
		$translator = new I18nTranslatorModel();

		$different_languages = $translator->getStats();

		$current_lang = false;
		$translations = false;
		$can_edit = false;
		$isAdmin = false;

		$this->assign( 'langs', $different_languages );

		$current_lang = $this->getCurrentLang();

		// The languages are defined with 5 chars. E.g: es_ES
		if ( $current_lang && 5 == strlen( $current_lang ) )
		{
			$translations = $translator->getTranslations( $current_lang );
		}

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
		return $this->getUrlParam( 0 );
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