<?php
	
class Email
{
	private $sender = "";
	private $recipient;
	private $subject;
	private $body;
	
	public function sender($mail)
	{
		$this->sender = "From: $mail\n";
		$this->sender .= "MIME-Version: 1.0\n";
		$this->sender .= "Content-type: text/html; charset=iso-8859-1\n";
	}
	
	public function recipient($mail)
	{
		$this->recipient = $mail;
	}
	
	public function subject($subject)
	{
		$this->subject = $subject;
	}
	
	public function body($title, $message, $url = '', $btnTitle)
	{
		$this->body = $this->bodyTemplate($title, $message, $url, $btnTitle);
	}
	
	public function send()
	{
		$query = mail($this->recipient, $this->subject, $this->body, $this->sender);
		if(!$query)
		{
			return false;
		}
		
		return true;
	}
	
	public function bodyTemplate($title, $message, $url, $btnTitle)
	{
		';
	}
	
}

?>