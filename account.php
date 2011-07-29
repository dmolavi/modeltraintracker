<?php
	require_once('auth.php');
	
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
	
	$account_query = "SELECT member_id, firstname, lastname, login FROM members WHERE member_id=".$_SESSION['SESS_MEMBER_ID'];
	$account_res = mysql_query($account_query);
	$account_data = array();
	$account_row = mysql_fetch_row($account_res);	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Model Train Inventory</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link type="text/css" href="css/layout.css" rel="stylesheet" />
<link type="text/css" href="css/jquery-ui-1.8.13.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.13.custom.min.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>
<script type="text/javascript"> 
	$(function(){
		$('input:submit,button').button();
		jQuery.validator.messages.required = "";
		$("#account_form").validate({
						rules: {
							firstname: "required",
							lastname: "required",
							username: { required: true, minlength: 5 },
							password1: { minlength: 8 },
							password2: { required: "#password1:filled", minlength: 8, equalTo: "#password1" }
						},
						messages: {
							firstname: "Please enter your first name",
							lastname: "Please enter your last name",
							username: {
								required: "Please enter a username",
								minlength: "Your username must consist of at least 5 characters"
							},
							password: {
								required: "Please provide a password",
								minlength: "Your password must be at least 8 characters long"
							},
							password2: {
								required: "Please provide a password",
								minlength: "Your password must be at least 8 characters long",
								equalTo: "Your confirmation password does not match the first entry."
							}						},
						submitHandler: function(form) {
											var newpassword;
											$("#password1").is(":filled") ? newpassword = $("#password1").val() :  newpassword = '';
											$("#msgbox").removeClass().addClass('messagebox').text('Updating....').fadeIn(1000);
											$.ajax({
												type: "POST",
												url: "edit.php",
												context: document.body,
												data:({login_id : $("#login_id").val(), firstname : $("#firstname").val(), username : $("#username").val(), lastname : $("#lastname").val(), password : newpassword , ref : "account" }),
												success: function(){
														$("#msgbox").fadeTo(200,0.1,function() //start fading the messagebox
														{ 
														  //add message and change the class of the box and start fading
														  $(this).html('Account successfully updated').addClass('messageboxok').fadeTo(900,1);
														});
												}
											});							
						}
		});	

});
</script>
</head>
<body>
<?php
include('menu.php');
?>
	<div id="content">
		<form action ="#" id="account_form">
		<fieldset class="ui-widget ui-widget-content ui-corner-all">
		<legend class="ui-widget ui-widget-header ui-corner-all">Account Settings</legend>
			<table>
			<tr>
			<td><label for="firstname">First Name:</label></td>
			<td><input name="firstname" type="text" id="firstname" value="<?php echo $account_row[1]; ?>" size="30" maxlength="50" /></td>
			</tr>
			<tr>
			<td><label for="lastname">Last Name:</label></td>
			<td><input name="lastname" type="text" id="lastname" value="<?php echo $account_row[2]; ?>" size="30" maxlength="50" /></td>
			</tr>
			<tr>
			<td><label for="username">Username:</label></td>
			<td><input name="username" type="text" id="username" value="<?php echo $account_row[3]; ?>" size="30" maxlength="50" /></td>
			</tr>
			<tr>
			<td><label for="password1">New Password:</label></td>
			<td><input name="password1" type="password" id="password1" value="" size="30" maxlength="50" /></td>
			</tr>
			<tr>
			<td><label for="password2">New Password (confirmation):</label></td>
			<td><input name="password2" type="password" id="password2" value="" size="30" maxlength="50" />
			<input type="hidden" name="login_id" id="login_id" value="<?php echo $account_row[0]; ?>" /></td>
			</tr>
			</table>
			<input type="submit" value="Submit" /> &nbsp; <span id="msgbox" style="display:none"></span>
		</form>
		</fieldset>
	</div>
<?php include('footer.php'); ?>
</body>
</html>	