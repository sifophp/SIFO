<?php
class DebugMailController extends Controller
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
		if( !($this->mail_data = Session::get( 'mail_data' ) ) )
		{
			throw new Exception_500( 'No exists mail data to send the mail' );
		}
		Session::delete( 'mail_data' );
		$mail = $this->getClass( 'Mail' );
		return $mail->send( $this->mail_data['to'], $this->mail_data['subject'], $this->mail_data['body'] );
	}

	public function build()
	{
		if ( !$this->hasDebug() )
		{
			throw Exception_404( 'Only in debug mode' );
		}
		$this->setLayout( 'debug/mail.tpl' );
		Session::getInstance();
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
			if ( FilterServer::getInstance()->getString( 'HTTP_REFERER' ) )
			{
				$new_mail_data['return_page'] = FilterServer::getInstance()->getString( 'HTTP_REFERER' );
			}
			// For can cotinue clicking the link:
			Session::set( 'mail_data', $new_mail_data );
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
