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
		return '
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<meta name="viewport" content="width=device-width" />
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<title>'.$title.'</title>
				<link href="'.BASE_URL.'email_templates/styles.css" media="all" rel="stylesheet" type="text/css" />
				<style>* {margin: 0;padding: 0;font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;box-sizing: border-box;font-size: 14px;}img {max-width: 100%;}
				body {-webkit-font-smoothing: antialiased;-webkit-text-size-adjust: none;width: 100% !important;			height: 100%;line-height: 1.6;}
				table td {vertical-align: top;}
				body {background-color: #f6f6f6;}
				.body-wrap {background-color: #f6f6f6;width: 100%;}
				.container {display: block !important;max-width: 600px !important;margin: 0 auto !important;clear: both !important;}
				.content {max-width: 600px;margin: 0 auto;
						display: block;
						padding: 20px;}
					
					.main {
						background: #fff;
						border: 1px solid #e9e9e9;
						border-radius: 3px;
					}
					
					.content-wrap {
						padding: 20px;
					}
					
					.content-block {
						padding: 0 0 20px;
					}
					
					.header {
						width: 100%;
						margin-bottom: 20px;
					}
					
					.footer {
						width: 100%;
						clear: both;
						color: #999;
						padding: 20px;
					}
					.footer a {
						color: #999;
					}
					.footer p, .footer a, .footer unsubscribe, .footer td {
						font-size: 12px;
					}
					
					
					h1, h2, h3 {
						font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
						color: #000;
						margin: 40px 0 0;
						line-height: 1.2;
						font-weight: 400;
					}
					
					h1 {
						font-size: 32px;
						font-weight: 500;
					}
					
					h2 {
						font-size: 24px;
					}
					
					h3 {
						font-size: 18px;
					}
					
					h4 {
						font-size: 14px;
						font-weight: 600;
					}
					
					p, ul, ol {
						margin-bottom: 10px;
						font-weight: normal;
					}
					p li, ul li, ol li {
						margin-left: 5px;
						list-style-position: inside;
					}
					
					
					a {
						color: #1ab394;
						text-decoration: underline;
					}
					
					.btn-primary {
						text-decoration: none;
						color: #FFF;
						background-color: #1ab394;
						border: solid #1ab394;
						border-width: 5px 10px;
						line-height: 2;
						font-weight: bold;
						text-align: center;
						cursor: pointer;
						display: inline-block;
						border-radius: 5px;
						text-transform: capitalize;
					}
					.last {
						margin-bottom: 0;
					}
					
					.first {
						margin-top: 0;
					}
					
					.aligncenter {
						text-align: center;
					}
					
					.alignright {
						text-align: right;
					}
					
					.alignleft {
						text-align: left;
					}
					
					.clear {
						clear: both;
					}
				</style>
			</head>

			<body>

			<table class="body-wrap">
				<tr>
					<td></td>
					<td class="container" width="600">
						<div class="content">
							<table class="main" width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td class="content-wrap">
										<table  cellpadding="0" cellspacing="0">
											<tr>
												<td>
													<img class="img-responsive" src="'.BASE_URL.'email_templates/img/email_header.png"/>
												</td>
											</tr>
											<tr>
												<td class="content-block">
													<h3>'.$title.'</h3>
												</td>
											</tr>
											<tr>
												<td class="content-block">'.$message.'</td>
											</tr>
											<tr>
												<td class="content-block aligncenter">
													<a href="'.$url.'" class="btn-primary">'.$btnTitle.'</a>
												</td>
											</tr>
										  </table>
									</td>
								</tr>
							</table>
							<div class="footer">
								<table width="100%">
									<tr>
										<td class="aligncenter content-block">Contact us via email <a href="mailto:support@japannotepc.com">Support</a> or call 12345678.</td>
									</tr>
									<tr>
										<td class="aligncenter content-block">Follow <a href="#">@japannotepc</a> on Facebook.</td>
									</tr>
									<tr>
										<td class="aligncenter content-block">Follow <a href="#">@japannotepc</a> on Twitter.</td>
									</tr>
									<tr>
										<td class="aligncenter content-block">For verification and authentication, please contact support <a href="mailto:support@japannotepc.com">Support</a></td>
									</tr>
									<tr>
										<td class="aligncenter content-block"><a href="'.BASE_URL.'unsubscribe">Unsubscribe</a></td>
									</tr>
								</table>
							</div></div>
					</td>
					<td></td>
				</tr>
			</table>

			</body>
			</html>

		
		';
	}
	
}

?>