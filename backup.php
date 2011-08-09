<?php

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
<title>Backup and Restore</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link type="text/css" href="css/layout.css" rel="stylesheet" />
<link type="text/css" href="css/prr/jquery-ui-1.8.14.custom.css" rel="stylesheet" />	
<link type="text/css" href="css/fileuploader.css" rel="stylesheet" />	

<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.13.custom.min.js"></script>
<script type="text/javascript" src="js/fileuploader.js"></script>
<script type="text/javascript"> 
	$(function(){
		$('#backup').button();
		$('a').button().width(110);		
	});
</script>
</head>
<body>		
<?php
include('menu.php');
?>
	<div id="content">
	<fieldset class="ui-widget ui-widget-content ui-corner-all">
	<legend class="ui-widget ui-widget-header ui-corner-all">Create backup</legend>
		Click the button below to create a backup of your item database.<br /><br />
		<form action="create_backup.php" method="get">
			<input type="submit" id="backup" value="Create backup file" />
		</form>
	</fieldset>	
	<br /><br />
	<fieldset class="ui-widget ui-widget-content ui-corner-all">
	<legend class="ui-widget ui-widget-header ui-corner-all">Upload backup</legend>
		Click the button below to upload a backup of your database.  Please note that doing so will drop all items before restoring from backup, so please ensure your backup is current.<br /> <br /> 
		<div id="file-upload">		
			<noscript>			
				<p>Please enable JavaScript to use file uploader.</p>
			</noscript>   
			<script>
				function createUploader(){            
					var uploader = new qq.FileUploader({
						element: document.getElementById('file-upload'),
						action: 'fileupload.php',
						template: '<div class="qq-uploader">' +
								  '<div class="qq-upload-button">Restore a backup</div>' +
								  '<ul class="qq-upload-list"></ul>' +
								  '</div>'
					});           
				};
				// in your app create uploader as soon as the DOM is ready
				// don't wait for the window to load  
				window.onload = createUploader;   				
			</script>
		</div> 
	</fieldset>
	</div>
<?php include('footer.php'); ?>	
</body>
