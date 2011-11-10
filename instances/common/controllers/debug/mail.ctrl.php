<?php
namespace Common;

namespace Common;
class DebugMailController extends \Sifo\Controller
{
	private $mail_data;
	
	/**
	 * List of classes that will be autoloaded automatically.
	 *
	 * Format: $include_classes = array( 'Metadata', 'FlashMessages', 'Session', 'Cookie' );
	 */
	protected $include_classes = array( 'Session' );

	private function continueMail()
	{
		if( !($this->mail_data = \Sifo\Session::get( 'mail_data' ) ) )
		{
			throw new \SifoException_500( 'No exists mail data to send the mail' );
		}
		\Sifo\Session::delete( 'mail_data' );
		$mail = new \SifoMail();
		return $mail->send( $this->mail_data['to'], $this->mail_data['subject'], $this->mail_data['body'] );
	}

	public function build()
	{
		if ( !$this->hasDebug() )
		{
			throw \SifoException_404( 'Only in debug mode' );
		}
		$this->setLayout( 'debug/mail.tpl' );
		\Sifo\Session::getInstance();
		if ( $this->getParam( 'current_url' ) == $this->getUrl( 'mail-continue' ) )
		{
			$this->assign( 'mail_sent', true );
			$this->assign( 'result', $this->continueMail() );

			if ( isset( $this->mail_data['return_page'] ) )
			{
				$this->assign( 'return_page', $this->mail_data['return_page'] );
			}
		}
		else
		{
			$new_mail_data = $this->getParam( 'mail_data' );
			if ( \Sifo\FilterServer::getInstance()->getString( 'HTTP_REFERER' ) )
			{
				$new_mail_data['return_page'] = \Sifo\FilterServer::getInstance()->getString( 'HTTP_REFERER' );
			}
			// For can cotinue clicking the link:
			\Sifo\Session::set( 'mail_data', $new_mail_data );
			$this->assign( 'mail_data', $new_mail_data );
			$this->assign( 'continue_sending', $this->getUrl( 'mail-continue' ) );
		}

	}

	public function getCacheDefinition()
	{
		// No caching:
		return false;
	}
}
?>
