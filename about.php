<?php
// MTT VERSION 1.0
session_start();

//Check whether the session variable SESS_MEMBER_ID is present or not
if(!isset($_SESSION['SESS_MEMBER_ID']) || (trim($_SESSION['SESS_MEMBER_ID']) == '')) {
	header("location: login.php");
	exit();
}

require_once('auth.php');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
<title>About ModelTrainTracker</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link type="text/css" href="css/layout.css" rel="stylesheet" />
<link type="text/css" href="css/prr/jquery-ui-1.8.14.custom.css" rel="stylesheet" />	

<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.13.custom.min.js"></script>
<script type="text/javascript"> 
	$(function(){
		$('a.menu').button().width(110);
	});
</script>
</head>
<body>		
<?php
include('menu.php');
?>
	<div id="content">
		ModelTrainTracker is grateful to the following organizations and development teams, without which this site could not exist:<br /><br />
			<a href="http://www.php.net/" target="blank"><img src="css/images/php-med-trans.png" /></a><br /><br />
			<a href="http://www.mysql.com/" target="blank"><img src="css/images/powered-by-mysql-125x64.png" /></a><br /><br />
			<a href="http://www.jquery.com/" target="blank"><img src="css/images/jQuery_logo_color_onwhite.png" /></a><br /><br />
			<a href="http://www.jqueryui.com/" target="blank"><img src="css/images/jQuery__UI_logo_color_onwhite.png" /></a><br /><br />
			<a href="http://www.datatables.net/" target="new"><img src="css/images/datatables_logo.png" /></a><br /><br />
	</div>
<?php include('footer.php'); ?>	
</body>
