<?php
require_once "Db.php";
require_once "config.php";
require_once "SendMail.php";
$getVal = $_GET;
$key = isset($getVal['un_subscribe']) ? 'un-subscribed' : 'verified';
$message = [
	"class" => "alert-danger",
	"message" => "You have already $key your account, Plese click on the below link to user another email address.",
];
if (isset($getVal['id']) && !empty($getVal['id'])) {
	$email_address = base64_decode($getVal['id']);
	$db = new Db(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
	$email = new SendMail(EMAIL_HOST, EMAIL_USERNAME, EMAIL_PASSWORD, EMAIL_PORT);

	$userData = $db->getDetails(['email' => $email_address], 'users');
	if (isset($userData['is_verified']) && $userData['is_verified'] == 'N') {
		$response = $db->updateUser(['email' => $email_address, 'type' => 1]);
		$postData = [
			'first_name' => $userData['first_name'],
			'last_name' => $userData['last_name'],
			'name' => $userData['first_name'] . " " . $userData['last_name'],
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
		$message = [
			"class" => "alert-success",
			"message" => "You have successfully verified your account, Plese check you email for further updates.",
		];
	}
}
if (isset($getVal['un_subscribe']) && !empty($getVal['un_subscribe'])) {
	$email_address = base64_decode($getVal['un_subscribe']);
	$db = new Db(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
	$userData = $db->getDetails(['email' => $email_address], 'users');
	if (isset($userData['is_active']) && $userData['is_active'] == 'Y') {
		$response = $db->updateUser(['email' => $email_address, 'type' => 2]);
		$message = [
			"class" => "alert-success",
			"message" => "You have successfully un-subscribed, Plese check you email for further updates.",
		];
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Email Subscription</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
	</head>
	<body class="container">
		<div class="row">
			<div class="offset-3 col-md-6 offset-3">
				<div class="card mt-5">
					<div class="card-header"> Subscription details</div>
					<div class="card-body">
						<div class="alert <?=$message['class'];?>" role="alert">
							<?=$message['message'];?>
						</div>
						<a href="index.php" class="btn btn-primary">Make another request</a>
					</div>
				</div>
			</div>
		</div>
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
	</body>
</html>