<?php
	session_start();
	
	//Check whether the session variable SESS_MEMBER_ID is present or not
	if(!isset($_SESSION['SESS_MEMBER_ID']) || (trim($_SESSION['SESS_MEMBER_ID']) == '')) {
		header("location: login.php");
		exit();
	} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Model Train Inventory</title>
<link type="text/css" href="css/jquery-ui-1.8.13.custom.css" rel="stylesheet" />	
<link type="text/css" href="css/layout.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.13.custom.min.js"></script>
</head>
<body>
<?php
include('menu.php');
?>
	<div id="content">
		Welcome to the Model Train Inventory website.
	</div>
<?php include('footer.php'); ?>
</body>
</html>