<?php
	/* PHP Script for AJAX Verifying */
	if(!isset($_POST['captcha_code']) || $_POST['captcha_code'] == "") {
		exit;
	}

	session_start();
	include_once('securimage/securimage.php');
	$securimage = new Securimage();

	if ($securimage->check($_POST['captcha_code']) == false) {
		echo "FALSE";
		$_SESSION['captcha'] = false;
	}
	else {
		echo "TRUE";
		$_SESSION['captcha'] = true;
	}

	exit;
?>
