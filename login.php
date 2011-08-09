<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Model Train Inventory</title>
<link type="text/css" href="css/layout.css" rel="stylesheet" />
<link type="text/css" href="css/prr/jquery-ui-1.8.14.custom.css" rel="stylesheet" />	
<link type="text/css" href="css/jquery.alerts.css" rel="stylesheet" />	

<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.13.custom.min.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/jquery.alerts.js"></script>
<script type="text/javascript" src="js/jquery.md5.js"></script>

<script type="text/javascript">
	$(function(){
		$('input:submit').button();
		$('a').button().width(110);		
		
		$("#loginForm").validate({
						rules: {
							login: "required",
							password: "required",
						},
						onkeyup: false,
						onclick: false,
						messages: { },
						invalidHandler: function(e, validator) {
							edit_errors = validator.numberOfInvalids();
							if (edit_errors) {
								var message = edit_errors == 1
									? 'You missed 1 field. It has been highlighted below'
									: 'You missed ' + edit_errors + ' fields.  They have been highlighted below';
								$("div.login_error span").html(message);
								$("div.login_error").show();
								$("#msgbox").hide();
							} else {
								$("div.login_error").hide();
							}
						}, 
						submitHandler: function(form) {
											$("div.login_error").hide();
											//remove all the class add the messagebox classes and start fading
											$("#msgbox").removeClass().addClass('messagebox').text('Validating....').fadeIn(1000);
											//check the username exists or not from ajax
											$.post("login_exec.php",{ login:$('#login').val(),password:$.md5($('#password').val()),rand:Math.random() } ,function(data)
											{
											  if(data=='yes') //if correct login detail
											  {
												$("#msgbox").fadeTo(200,0.1,function()  //start fading the messagebox
												{ 
												  //add message and change the class of the box and start fading
												  $(this).html('Logging in.....').addClass('messageboxok').fadeTo(900,1,
												  function()
												  { 
													 //redirect to secure page
													 document.location='items.php';
												  });
												  
												});
											  }
											  else 
											  {
												$("#msgbox").fadeTo(200,0.1,function() //start fading the messagebox
												{ 
												  //add message and change the class of the box and start fading
												  $(this).html('Invalid Login').addClass('messageboxerror').fadeTo(900,1);
												});		
											  }
													
											});
											return false; //not to post the  form physically						
						}
		});			
	});
</script>
</head>
<body onload="document.loginForm.login.focus();">
<?php
include('menu.php');
?>
	<div id="content">
	<div class="login_error" style="display:none;">
		<img src="css/images/important.gif" alt="Warning!" width="24" height="24" style="float:left; margin: -5px 10px 0px 0px; " />
		<span></span>.<br clear="all" />
	</div>	
	<form id="loginForm" name="loginForm" method="post" action="">
		<label for="login">Login</label>
		<input name="login" type="text" class="textfield" id="login" value="testuser" />
		<br />
		<label for="password">Password</label>
		<input name="password" type="password" class="textfield" id="password" value="testuser" />
		<br /><br />
		<input name="Submit" type="submit" id="submit" value="Login" class="button" />
		<span id="msgbox" style="display:none"></span>
	</form>
</div>
<?php include('footer.php'); ?>
</body>
</html>