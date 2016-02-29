<?php

require __DIR__.'/../vendor/autoload.php';

/*
	A simple mail object

	Author
		Aurélien Delogu (dev@dreamysource.fr)
*/
class Mail extends Chernozem {

	/*
		string, array from  : a contact list representing the persons who sent the message
		string sender       : the real sender
		string replyto      : the address where replies are sent to
		string, array to    : a contact list where the mail will be sent
		string, array cc    : a contact list where the mail will be copied
		string, array bcc   : a contact list where the mail will be blind-copied
		string subject      : the subject of the message
		string body         : the body of the message
		string html         : the html body
		array attachments   : an attachment list
	*/
	protected $from;
	protected $sender;
	protected $replyto;
	protected $to;
	protected $cc;
	protected $bcc;
	protected $subject;
	protected $body;
	protected $html;
	protected $attachments;

	/*
		Send the e-mail

		Return
			Mail
	*/
	public function send() {
		// Verification
		if(!$this->from) {
			throw new Exception("The 'from' property must be defined");
		}
		if(!$this->to) {
			throw new Exception("The 'to' property must be defined");
		}
		if(!$this->subject) {
			throw new Exception("The 'subject' property must be defined");
		}
		if(!$this->body && !$this->html) {
			throw new Exception("The 'body' or 'html' property must be defined");
		}
		// Define senders/recipients
		$headers = 'From: '.$this->_prepareContacts($this->from)."\r\n";
		if($this->replyto) {
			$headers .= 'Reply-To: '.(string)$this->replyto."\r\n";
		}
		if($this->sender) {
			$headers .= 'Sender: '.(string)$this->sender."\r\n";
		}
		if($this->cc) {
			$headers .= 'Cc: '.$this->_prepareContacts($this->cc)."\r\n";
		}
		if($this->bcc) {
			$headers .= 'Bcc: '.$this->_prepareContacts($this->bcc)."\r\n";
		}
		$headers .= 'Date: '.date('r')."\r\n";
		// Init message type
		$headers .= 'Mime-Version: 1.0'."\r\n";
		if(($this->body && $this->html) || $this->attachments) {
			$multipart = true;
			$boundary = md5(uniqid(microtime(), true));
			$headers .= "Content-Type: multipart/mixed; boundary=$boundary; charset=utf-8\r\n";
			$message = "Your mail client doesn't support 'multipart/mixed' data, please update it to have full mail support.\r\n\r\n";
		}
		elseif($this->html) {
			$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
		}
		else{
			$headers .= 'Content-type: text/plain; charset=utf-8'."\r\n";
		}
		// Add text body
		if($this->body) {
			if($multipart) {
				$message .= '--'.$boundary."\r\n";
				$message .= 'Content-type: text/plain; charset=utf-8'."\r\n";
				$message .= 'Content-transfer-encoding: 8bit'."\r\n\r\n";
				$message .= wordwrap($this->body,70)."\r\n";
			}
			else{
				$message = wordwrap($this->body,70);
			}
		}
		// Add HTML body
		if($this->html) {
			if($multipart) {
				$message .= '--'.$boundary."\r\n";
				$message .= 'Content-type: text/html; charset=utf-8'."\r\n\r\n";
				$message .= $this->html."\r\n";
			}
			else{
				$message = $this->html;
			}
		}
		// Add attachments
		if($this->attachments) {
			// Init FileInfo
			if(!$finfo = new \finfo(FILEINFO_MIME)) {
				throw new Exception("Cannot open the magic mime types database");
			}
			// Add attachments
			foreach((array)$this->attachments as $name => $file) {
				if(is_file($file)) {
					if(is_int($name)) {
						$name = substr($file, strrpos($file, '/') + 1);
					}
					$message .= '--'.$boundary."\r\n";
					$message .= 'Content-type: '.$finfo->file($file)."; name=$name\r\n";
					$message .= 'Content-transfer-encoding: base64'."\r\n\r\n";
					$message .= chunk_split(base64_encode(file_get_contents($file)))."\r\n";
				}
			}
		}
		// Close message
		$headers .= "\r\n";
		if($multipart) {
			$message .= '--'.$boundary."\r\n";
		}
		// Mail sending
		if(!mail($this->_prepareContacts($this->to), $this->subject, $message, $headers)) {
			throw new Exception("Fail to send '$this->subject' e-mail");
		}
		return $this;
	}

	/*
		Validate an e-mail address

		Parameters
			string $email

		Return
			boolean
	*/
	static public function validate($email) {
		return (bool)preg_match('#^[-+.\w]{1,64}@[-.\w]{1,64}\.[-.\w]{2,6}$#', (string)$email);
	}

	/*
		Create a contact list from an e-mail list

		Parameters
			array $emails

		Return
			string
	*/
	protected function _prepareContacts($emails) {
		$contacts = array();
		foreach((array)$emails as $name => $email) {
			if(!self::validate($email)) {
				throw new Exception("Invalid '$email' address");
			}
			if(is_string($name)) {
				$contacts[] = "$name <$email>";
			}
			else{
				$contacts[] = "<$email>";
			}
		}
		return implode(',', $contacts);
	}

}
