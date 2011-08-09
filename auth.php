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

	require_once('config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link) {
		die('Failed to connect to server: ' . mysql_error());
	}
	
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db) {
		die("Unable to select database");
	}
	
	function is_admin() {
		$admin_query = "SELECT admin FROM members WHERE member_id = ".$_SESSION['SESS_MEMBER_ID'];
		$admin_result = mysql_query($admin_query) or die(mysql_error());
		$admin_row = mysql_fetch_row($admin_result);
		if($admin_row[0] > 0) {
			return true;
		} else {
			return false;
		}
	}
?>