<?php
namespace Common;

include_once ROOT_PATH . '/instances/common/controllers/shared/commandLine.ctrl.php';

class ScriptsSendMailController extends SharedCommandLineController
{
	private $_subject;
	private $_body;
	private $_html_mode = false;

	protected function sendMail()
	{
		if ( isset( $this->recipient ) || !empty( $this->recipient_list ) )
		{
			if ( !empty( $this->recipient ) )
			{
				$this->recipient_list = array_merge( $this->recipient_list, array_diff( $this->recipient, $this->recipient_list ) );
			}

			$mail = new \Sifo\Mail();
			$subject = $this->_subject;
			$content = $this->_body;

			$mail->IsHTML( $this->_html_mode );

			foreach ( $this->recipient_list as $recipient )
			{
				$this->showMessage( "Now I would try send an email with subject: '" . $subject . "' to '" . $recipient . "'", self::TEST );
				if ( !$this->test )
				{
					$mail->send( $recipient, $subject, $content );
				}
			}
		}
	}
	// ABSTRACTED METHODES:

	public function init()
	{
		$this->help_str = 'Send a mail using the instance mail configuration. '.PHP_EOL;

		$this->setNewParam( 'S', 'subject', 'Mail subject.', true, true );
		$this->setNewParam( 'F', 'filecontent', 'Mail body. Don\'t define this params to send  the stdin', true, true );
		$this->setNewParam( 'H', 'html', 'Use this flag to send the mail with html code. Text is default.', false, false );
	}
	
	public function exec()
	{
		$this->showMessage( "Starting the script", self::VERBOSE );
		foreach ( $this->command_options as $option )
		{
			switch ( $option[0] )
			{
				case "S":
				case "subject":
					$this->_subject = $option[1];
					break;
				case "F":
				case "filecontent":
					$content_path = $option[1];
					break;
				case "H":
				case "html":
					$this->_html_mode = true;
					break;
			}
		}

		if ( !isset( $content_path ) || ( !( $this->_body = @file_get_contents( $content_path ) ) ) )
		{
			die( "Mail body not found." );
		}

		// Desist to search the sendmail command, it's requested in the the parent Class.
		$this->showMessage( "Finishing!", self::VERBOSE );
	}
}
?>