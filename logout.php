<?php
	//Start session
	session_start();
	
	//Unset the variables stored in session
	unset($_SESSION['SESS_MEMBER_ID']);
	unset($_SESSION['SESS_FIRST_NAME']);
	unset($_SESSION['SESS_LAST_NAME']);
	session_destroy();
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
<script type="text/javascript">

</script>
<style type="text/css">
</style>
<body>
<?php
include('menu.php');
?>
	<div id="content">
			<h4 align="center" class="err">You have been logged out.</h4>
			<p align="center">Click here to <a href="login.php">Login</a></p>
	</div>
<?php include('footer.php'); ?>
</body>
</html>
