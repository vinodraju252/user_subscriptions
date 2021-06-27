<?php
require_once "config.php";
require_once "SendMail.php";
require_once "Db.php";
$postVal = $_POST;
if (isset($postVal['submit']) && $postVal['submit'] == 'submit') {
	$error = "";
	$first_name = trim($postVal['first_name']);
	$last_name = trim($postVal['last_name']);
	$email_address = trim($postVal['email']);
	if (empty($first_name)) {
		$error .= "<li>First name is required</li>";
	}
	if (empty($last_name)) {
		$error .= "<li>Last name is required</li>";
	}
	if (empty($email_address)) {
		$error .= "<li>Email is required</li>";
	}

	if (empty($error)) {
		$db = new Db(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		$email = new SendMail(EMAIL_HOST, EMAIL_USERNAME, EMAIL_PASSWORD, EMAIL_PORT);
		$userData = $db->getDetails($postVal, 'users');
		if (isset($userData['is_verified']) && $userData['is_verified'] == 'N') {
			$postData = [
				'first_name' => $first_name,
				'last_name' => $last_name,
				'name' => $first_name . " " . $last_name,
				'to_email' => $email_address,
				'from_name' => EMAIL_FROM_NAME,
				'from_email' => EMAIL_FROM_ADDRESS,
				'subject' => "Subscribe to email alert..!",
				'subscribe_link' => SITE_LINK . "verify.php?id=" . base64_encode($email_address),
				'un_subscribe_link' => SITE_LINK . "verify.php?un_subscribe=" . base64_encode($email_address),
			];
			$response = $email->sendEmail($postData, 'subscribe');
		} elseif (!isset($userData['is_verified']) || count($userData) == 0) {
			$postData = [
				'first_name' => $first_name,
				'last_name' => $last_name,
				'name' => $first_name . " " . $last_name,
				'to_email' => $email_address,
				'from_name' => EMAIL_FROM_NAME,
				'from_email' => EMAIL_FROM_ADDRESS,
				'subject' => "Subscribe to email alert..!",
				'subscribe_link' => SITE_LINK . "verify.php?id=" . base64_encode($email_address),
				'un_subscribe_link' => SITE_LINK . "verify.php?un_subscribe=" . base64_encode($email_address),
			];
			$response = $email->sendEmail($postData, 'subscribe');
			if ($response['status'] == 'success') {
				$db->insertUser($postData);
			}
		} elseif (isset($userData['is_verified']) && $userData['is_verified'] == 'Y') {
			$postData = [
				'first_name' => $first_name,
				'last_name' => $last_name,
				'name' => $first_name . " " . $last_name,
				'to_email' => $email_address,
				'from_name' => EMAIL_FROM_NAME,
				'from_email' => EMAIL_FROM_ADDRESS,
				'user_id' => $userData['id'],
				'un_subscribe_link' => SITE_LINK . "verify.php?un_subscribe=" . base64_encode($email_address),
			];
			$contentData = $email->getMessageContents();
			$postData = array_merge($postData, $contentData);
			$response = $email->sendEmail($postData, 'message');
			if ($response['status'] == 'success') {
				$db->insertNotification($postData);
			}
		}
		echo json_encode($response);
	} else {
		$response = ["status" => "fail", "message" => "<ul>" . $error . "</ul>", "class" => "alert-danger"];
		echo json_encode($response);
	}
}
exit;