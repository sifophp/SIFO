<?php

/**
 * Email configuration. More options can be added. See Mail class.
 *
 * The key values must be PHPMailer attributes. You can remove attributes, defaults will be used
 *
 */
$config['From'] 		= 'noreply@localhost';
$config['FromName'] 	= 'Sifo Administrator';
$config['CharSet'] 		= 'utf-8';
$config['ContentType']	= 'text/html';

//  Method to send mail: ("mail", "sendmail", or "smtp").
$config['Mailer'] 		= 'mail';
$config['Sendmail']		= '/usr/sbin/sendmail';

// SMTP settings (apply if mailer is smtp)
$config['Host']			= 'localhost';
$config['Port']			= 25;
$config['Host']			= 'localhost';
$config['Host']			= 'localhost';

// Options are "", "ssl" or "tls"
$config['SMTPSecure']	= '';
$config['SMTPAuth']		= false;
$config['Username']		= '';
$config['Password']		= '';
$config['Timeout']		= 10;
$config['SMTPDebug']	= false;