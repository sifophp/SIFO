<?php
// Use this class only for send mails from web (no scripts). (With debug pause).
class MailExt extends Mail
{
	/**
	 * Send an email whith debug interruption.
	 *
	 * @param string $to
	 * @param string $subject
	 * @param string $body
	 * @return boolean
	 */
	public function send( $to, $subject, $body )
	{
		// Debug advice:
		if ( Domains::getInstance()->getDevMode() )
		{
			$this->_dispatchMailController( $to, $subject, $body );
		}
		return parent::send( $to, $subject, $body );
	}

	private static function _dispatchMailController( $to, $subject, $body )
	{
		$ctrl = Bootstrap::invokeController( 'debug/mail' );
		$mail_data = array(
			'to' 	=> $to,
			'subject' => $subject,
			'body'	=> $body,
		);
		$ctrl->addParams( array( 'mail_data' => $mail_data ) );

		$ctrl->dispatch();

		exit;
	}

}
?>
