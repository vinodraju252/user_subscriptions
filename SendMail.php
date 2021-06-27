<?php
use PHPMailer\PHPMailer\PHPMailer;

require 'vendor/autoload.php';
require_once "EmailParser.php";
require_once "Db.php";
/**
 * Send Email Class
 */
class SendMail {
	private $_host;
	private $_username;
	private $_password;
	private $_post;

	/**
	 * Need to construct required details
	 */
	function __construct($host, $user, $pass, $port) {
		$this->_host = $host;
		$this->_username = $user;
		$this->_password = $pass;
		$this->_port = $port;
	}

	/**
	 * Send mail to users
	 */
	function sendEmail($postVal, $template) {
		$response = ["status" => "fail", "message" => "Failed to send mail, please try again later.", "class" => "alert-danger"];
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->Mailer = "smtp";

		// $mail->SMTPDebug = 1;
		$mail->SMTPAuth = TRUE;
		$mail->SMTPSecure = "tls";
		$mail->Port = $this->_port;
		$mail->Host = $this->_host;
		$mail->Username = $this->_username;
		$mail->Password = $this->_password;

		$mail->IsHTML(true);
		$mail->AddAddress($postVal['to_email'], $postVal['name']);
		$mail->SetFrom($postVal['from_email'], $postVal['from_name']);
		$mail->AddReplyTo($postVal['from_email'], $postVal['from_name']);
		$mail->Subject = $postVal['subject'];
		$html = $this->getHTML($postVal, $template);
		$mail->msgHTML($html);
		$file_name = '';
		if (isset($postVal['attachment'])) {
			$image = explode('/', $postVal['attachment']);
			$image = array_reverse($image);

			$file = file_get_contents($postVal['attachment']);
			$file_name = 'images/' . $image[0];
			file_put_contents($file_name, $file);
			$mail->addAttachment($file_name);
		}
		if ($mail->Send()) {
			$response = ["status" => "success", "message" => "Email has been sent successfully.", "class" => "alert-success"];
		}
		if (strlen($file_name) > 0) {
			unlink($file_name);
		}

		return $response;
	}

	function getHTML($postVal, $template) {
		$parser = new EmailParser();
		$html = $parser->parse($postVal, $template);
		return $html;
	}

	function getMessageContents() {
		$url = "https://xkcd.com/614/info.0.json";
		$url = explode('/', $url);
		$url[3] = rand(600, 620);
		$url = implode('/', $url);
		$response = [];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec($ch);
		$result = json_decode($result, true);
		if (isset($result['num'])) {
			$res = $result;
			$response['message'] = $res['transcript'];
			$response['alt'] = $res['alt'];
			$response['subject'] = $res['safe_title'];
			$response['data_url'] = $url;
			$response['attachment'] = $response['comic_image'] = $res['img'];
		}

		return $response;
	}
}