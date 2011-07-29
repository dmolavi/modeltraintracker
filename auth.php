<?php
if (eregi("auth.php",$_SERVER['SCRIPT_NAME'])) {
    Header("Location: index.php");
    die();
}

	//Start session
	session_start();

	//Check whether the session variable SESS_MEMBER_ID is present or not
	if(!isset($_SESSION['SESS_MEMBER_ID']) || (trim($_SESSION['SESS_MEMBER_ID']) == '')) {
		//$_SESSION['login_page_ref'] = parse_url($_SERVER['HTTP_REFERER'],PHP_URL_PATH);
		header("location: login.php");
		exit();
	}
?>