<?php
/**
 * Basado en smtp.php $Id: S5MailSender.class.php,v 1.1 2005/07/15 17:20:16 vherrera Exp $ de phpbb2 (C) 2001 The phpBB Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 * @package SistemasCinco
 */
class S5MailSender {
//-----------------------------
 public $smtp_username;
 public $smtp_password;
 public $smtp_host;
 public $email_from;
 //---------------------------
 function NSMailSender($smtp_host='', $email_from='' , $smtp_username='', $smtp_password='') {
   $this->smtp_host = $smtp_host;
   $this->smtp_username = $smtp_username;
   $this->smtp_password = $smtp_password;
   $this->email_from = $email_from;
 }
 //--------------------------
 function message_die($error_type, $error_message, $line, $file) {
  echo ("**Error: $error_type, $error_message, line $line, file $file<br />");
  return false;
 }
//-----------------------------
  function server_parse($socket, $response, $line = __LINE__) {
  	while (substr($response, 3, 1) != ' ') {
  		if (!($response = fgets($socket, 256))) {
  			$this->message_die('GENERAL_ERROR', "Couldn't get mail server response codes", "", $line, __FILE__);
  			  return false;
  		}
  	}

  	if (!(substr($response, 0, 3) == $response)) {
  		$this->message_die('GENERAL_ERROR', "Ran into problems sending Mail. Response: $response", "", $line, __FILE__);
  		  return false;
  	}
  	return true;
  }
  //----------------------------------------------------------------------------
  // Replacement or substitute for PHP's mail command
  function smtpmail($mail_to, $subject, $message, $headers = '', $mail_from = '') {

  	// Fix any bare linefeeds in the message to make it RFC821 Compliant.
  	$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);

  	if ($headers != '')
  	{
  		if (is_array($headers))
  		{
  			if (sizeof($headers) > 1)
  			{
  				$headers = join("\n", $headers);
  			}
  			else
  			{
  				$headers = $headers[0];
  			}
  		}
  		$headers = chop($headers);

  		// Make sure there are no bare linefeeds in the headers
  		$headers = preg_replace('#(?<!\r)\n#si', "\r\n", $headers);

  		// Ok this is rather confusing all things considered,
  		// but we have to grab bcc and cc headers and treat them differently
  		// Something we really didn't take into consideration originally
  		$header_array = explode("\r\n", $headers);
  		@reset($header_array);

  		$headers = '';
  		$a = list(, $header) = each($header_array);
  		while( $a )
  		{
  			if (preg_match('#^cc:#si', $header))
  			{
  				$cc = preg_replace('#^cc:(.*)#si', '\1', $header);
  			}
  			else if (preg_match('#^bcc:#si', $header))
  			{
  				$bcc = preg_replace('#^bcc:(.*)#si', '\1', $header);
  				$header = '';
  			}
  			$headers .= ($header != '') ? $header . "\r\n" : '';
  			$a = list(, $header) = each($header_array);
  		}

  		$headers = chop($headers);
  		$cc = explode(', ', $cc);
  		$bcc = explode(', ', $bcc);
  	}

  	if (trim($subject) == '')
  	{
  		$this->message_die('GENERAL_ERROR', "No email Subject specified", "", __LINE__, __FILE__);
  		  return false;
  	}

  	if (trim($message) == '')
  	{
  		$this->message_die('GENERAL_ERROR', "Email message was blank", "", __LINE__, __FILE__);
  		  return false;
  	}

  	// Ok we have error checked as much as we can to this point let's get on
  	// it already.
  	if( !$socket = fsockopen($this->smtp_host, 25, $errno, $errstr, 20) )
  	{
  		$this->message_die('GENERAL_ERROR', "Could not connect to smtp host : $errno : $errstr", "", __LINE__, __FILE__);
  		  return false;
  	}

  	// Wait for reply
  	if(!$this->server_parse($socket, "220", __LINE__)) return false;

  	// Do we want to use AUTH?, send RFC2554 EHLO, else send RFC821 HELO
  	// This improved as provided by SirSir to accomodate
  	if( !empty($this->smtp_username) && !empty($this->smtp_password) )
  	{
  		fputs($socket, "EHLO " . $this->smtp_host . "\r\n");
  		if(!$this->server_parse($socket, "250", __LINE__)) return false;

  		fputs($socket, "AUTH LOGIN\r\n");
  		if(!$this->server_parse($socket, "334", __LINE__)) return false;

  		fputs($socket, base64_encode($this->smtp_username) . "\r\n");
  		if(!$this->server_parse($socket, "334", __LINE__)) return false;

  		fputs($socket, base64_encode($this->smtp_password) . "\r\n");
  		if(!$this->server_parse($socket, "235", __LINE__)) return false;
  	}
  	else
  	{
  		fputs($socket, "HELO " . $this->smtp_host . "\r\n");
  		if(!$this->server_parse($socket, "250", __LINE__)) return false;
  	}

  	// From this point onward most server response codes should be 250
  	// Specify who the mail is from....
  	if ($mail_from=='') $mail_from = $this->email_from;
  	
  	fputs($socket, "MAIL FROM: <" . $mail_from . ">\r\n");
  	if(!$this->server_parse($socket, "250", __LINE__)) return false;

  	// Specify each user to send to and build to header.
  	$to_header = '';

  	// Add an additional bit of error checking to the To field.
  	$mail_to = (trim($mail_to) == '') ? 'Undisclosed-recipients:;' : trim($mail_to);
  	if (preg_match('#[^ ]+\@[^ ]+#', $mail_to))
  	{
  		fputs($socket, "RCPT TO: <$mail_to>\r\n");
  		if(!$this->server_parse($socket, "250", __LINE__)) return false;
  	}

  	// Ok now do the CC and BCC fields...
  	@reset($bcc);
  	$a = list(, $bcc_address) = each($bcc);
  	while( $a )
  	{
  		// Add an additional bit of error checking to bcc header...
  		$bcc_address = trim($bcc_address);
  		if (preg_match('#[^ ]+\@[^ ]+#', $bcc_address))
  		{
  			fputs($socket, "RCPT TO: <$bcc_address>\r\n");
  			if(!$this->server_parse($socket, "250", __LINE__)) return false;
  		}
  		$a = list(, $bcc_address) = each($bcc);
  	}

  	@reset($cc);
  	$a = list(, $cc_address) = each($cc);
  	while( $a )
  	{
  		// Add an additional bit of error checking to cc header
  		$cc_address = trim($cc_address);
  		if (preg_match('#[^ ]+\@[^ ]+#', $cc_address))
  		{
  			fputs($socket, "RCPT TO: <$cc_address>\r\n");
  			if(!$this->server_parse($socket, "250", __LINE__)) return false;
  		}
  		$a = list(, $cc_address) = each($cc);
  	}

  	// Ok now we tell the server we are ready to start sending data
  	fputs($socket, "DATA\r\n");

  	// This is the last response code we look for until the end of the message.
  	if(!$this->server_parse($socket, "354", __LINE__)) return false;

  	// Send the Subject Line...
  	fputs($socket, "Subject: $subject\r\n");

  	// Now the To Header.
  	fputs($socket, "To: $mail_to\r\n");

  	// Now any custom headers....
  	fputs($socket, "$headers\r\n\r\n");

  	// Ok now we are ready for the message...
  	fputs($socket, "$message\r\n");

  	// Ok the all the ingredients are mixed in let's cook this puppy...
  	fputs($socket, ".\r\n");
  	if(!$this->server_parse($socket, "250", __LINE__)) return false;

  	// Now tell the server we are done and close the socket...
  	fputs($socket, "QUIT\r\n");
  	fclose($socket);

  	return TRUE;
  }
//------------------------
} //end class
?>
