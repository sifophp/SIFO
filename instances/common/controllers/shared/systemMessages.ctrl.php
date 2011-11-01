<?php
namespace Common;

namespace Common;

class SharedSystemMessagesController extends \Sifo\Controller
{
	public function build()
	{
		$this->getClass( 'FlashMessages', false );
		$this->setLayout( 'shared/system_messages.tpl' );

		$this->assign( 'info_messages', \Sifo\FlashMessages::get( \Sifo\FlashMessages::MSG_INFO ) );
		$this->assign( 'ok_messages', \Sifo\FlashMessages::get( \Sifo\FlashMessages::MSG_OK ) );
		$this->assign( 'warning_messages', \Sifo\FlashMessages::get( \Sifo\FlashMessages::MSG_WARNING ) );
		$this->assign( 'ko_messages', \Sifo\FlashMessages::get( \Sifo\FlashMessages::MSG_KO ) );
	}
}