<?php
/*
TO DO:
1. Edit users
	- row_selected enables edit button
	- Modify login id, email
	- Send new password
*/
	require_once('auth.php');

	if(!is_admin()) {
		Header("Location: index.php");
		die();	
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
	   
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
<title>Model Train Inventory</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<link type="text/css" href="css/layout.css" rel="stylesheet" />
<link type="text/css" href="css/prr/jquery-ui-1.8.14.custom.css" rel="stylesheet" />	
<link type="text/css" href="css/jquery.alerts.css" rel="stylesheet" />	

<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.13.custom.min.js"></script>
<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/jquery.jeditable.mini.js"></script>
<script type="text/javascript" src="js/jquery.alerts.js"></script>
<script type="text/javascript" src="js/jquery.dataTables.editable.js"></script>

<script type="text/javascript"> 
	$(function(){
		var clickedRowId;
		
		$('input:submit,button').button(); 
		$('a').button().width(110);

		oTable = $('#user_table').dataTable({
			"bFilter": true,
			"bJQueryUI": true,
			"iDisplayLength": 25,
			"sPaginationType": "full_numbers",
			"sDom": '<"H"lfr>t<"F"ip>',
			"bSort": true,
			"bFilter": false,
			"bProcessing": true,
			"bServerSide":true,
			"sAjaxSource": "users.php",
			"fnServerData": function ( sSource, aoData, fnCallback ) {
						aoData.push( { "name": "ref", "value": "get_all" } );
						$.ajax( {
							"dataType": 'json', 
							"type": "POST", 
							"url": sSource, 
							"data": aoData, 
							"success": fnCallback
						} );
					},
			"bAutoWidth": false,
			"aoColumns": [
			{ "bSearchable": true, "bSortable": false, "bVisible": false, "sClass": "read_only" },			
            { "bSearchable": true, "bSortable": true, "sClass": "read_only" },
			{ "bSearchable": true, "bSortable": true, "sClass": "read_only" },
			{ "bSearchable": true, "bSortable": true, "sClass": "read_only" },
			]			
		}).makeEditable({
		   	sDeleteURL: "users.php",
			oDeleteParameters: {  ref: "delete" },
			sAddURL: "users.php",
			oAddNewRowButtonOptions: {	label: "Add user",
										icons: {primary:'ui-icon-plus'}},
			oDeleteRowButtonOptions: {	label: "Delete User", 
										icons: {primary:'ui-icon-trash'}},
			oAddNewRowFormOptions: {	title: 'Add user',
										width: 250,
										modal: true,
										draggable: true
									},
			oAddNewRowCancelButtonOptions: { 
										name: "action",
										value: "cancel-add",
										icons: { primary: 'ui-icon-close' }},
			oAddNewRowOkButtonOptions: {
										icons: {primary:'ui-icon-check'},
										name:"action",
										value:"add-new"},		
			fnShowError: function (message, action) {
				switch (action) {
					case "update":
						jAlert(message, "Update failed");
						break;
					case "delete":
						jAlert(message, "Delete failed");
						break;
					case "add":
						break;
				}
			},
			fnStartProcessingMode: function () {
				$("#processing_message").dialog();
			},
			fnEndProcessingMode: function () {
				$("#processing_message").dialog("close");
			},
			fnOnDeleting: function (tr, id, fnDeleteRow) {
				jConfirm('Please confirm that you want to delete this user?', 'Confirm Delete', function (r) {
					if (r) {
						fnDeleteRow(id);
						clickedRowId = 0;
						$('#btnEditRow').button( "option", "disabled", true );
					}
				});
				return false;
			},
			fnOnAdded: function(status)	{ 	
					oTable.fnDraw();
				}			
		});

		$("#user_table tr").live('click',function () {
			if ($(this).hasClass("row_selected") && $(this).attr('id') !== clickedRowId) {
				$('#btnEditRow').button( "option", "disabled", false );
				clickedRowId = $(this).attr('id');
			} else if($(this).attr('id') === clickedRowId) {
				$('#btnEditRow').button( "option", "disabled", true );
				$(this).removeClass("row_selected");
				clickedRowId = 0;
			} else {
				$('#btnEditRow').button( "option", "disabled", true );
				clickedRowId = 0;
			}
			return false;
		});			
		
		$('#btnEditRow').button({icons: {primary:'ui-icon-pencil'},
						 disabled: true
						}).click(function() {
							$( "#edituser" ).dialog( "open" );
							$.ajax({
								type: "POST",
								dataType: "json",
								data: ({id : clickedRowId, ref : "edit", action : "fetch" }),
								context: "#frm_edituser",
								url: "users.php", 
								success: function(data) {
									$("#e_firstname").val(data.firstname);
									$("#e_lastname").val(data.lastname);
									$("#e_login").val(data.login);
									$("#e_email").val(data.email);
									$("#member_id").val(data.member_id);
								}
							});
						});		
		
		$("#edituser").dialog({
			title: "Edit User",
			autoOpen: false,
			width: 250,
			modal: true,
			buttons: {
				"Edit User": function() {
					if($("#frm_edituser").valid()) {
						$("#frm_edituser").submit();
						$( this ).dialog( "close" );
					}
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				$(':text',"#frm_edituser").val("");
			}
		});	
		
		$("#lastname").blur(function() {
			if(($("#firstname").val() !== '') && ($("#lastname").val() !== '')) {
				$("#login").val($("#firstname").val().substring(0,1)+$("#lastname").val());
			}
		});
		
		jQuery.validator.messages.required = "";
		$("#formAddNewRow").validate({
				rules: {
					firstname: "required",
					lastname: "required",
					login: "required",
					email: { required: true, email: true},
					password1: "required",
					password2: { required: true, equalTo: "#password1" }
				},
				onkeyup: false,
				onclick: false,
				messages: { },
				invalidHandler: function(e, validator) {
					var errors = validator.numberOfInvalids();
					if (errors) {
						var message = errors == 1
							? 'You missed 1 field. It has been highlighted below'
							: 'You missed ' + errors + ' fields.  They have been highlighted below';
						$("div.error span").html(message);
						$("div.error").show();
					} else {
						$("div.error").hide();
					}
				}
		});
		$("#frm_edituser").validate({
				rules: {
					e_firstname: "required",
					e_lastname: "required",
					e_login: "required",
					e_email: { required: true, email: true},
					e_password2: { required: "#e_password1:filled", equalTo: "#e_password1" }
				},
				onkeyup: false,
				onclick: false,
				messages: { },
				invalidHandler: function(e, validator) {
					var errors = validator.numberOfInvalids();
					if (errors) {
						var message = errors == 1
							? 'You missed 1 field. It has been highlighted below'
							: 'You missed ' + errors + ' fields.  They have been highlighted below';
						$("div.error span").html(message);
						$("div.error").show();
					} else {
						$("div.error").hide();
					}
				}, 
				submitHandler: function(form) {
									$.ajax({
										type: "POST",
										url: "users.php",
										context: document.body,
										data:({id : $("#member_id").val(), firstname:$("#e_firstname").val(), lastname:$("#e_lastname").val(), login:$("#e_login").val(), email:$("#e_email").val(), password1:$("#e_password1").val(), ref : "edit", action : "edit" }),
										success: function(){
											oTable.fnDraw();
										}
									});
								}
				});
	});
</script>
<style type="text/css">
	.css_right {float:right}
	div.innerDetails { display: none }	
</style>
</head>
<body>
<?php
include('menu.php');
?>
	<div id="content">
		<button id="btnAddNewRow">Add User</button>&nbsp;<button id="btnEditRow">Edit User</button><button id="btnDeleteRow" style="float:right">Delete User</button><br /><br />
		<div id="processing_message" style="display:none" title="Processing">Please wait while your request is being processed...</div>	
		<form id="formAddNewRow" action="#">
			<div class="error" style="display:none;">
				<img src="css/images/important.gif" alt="Warning!" width="24" height="24" style="float:left; margin: -5px 10px 0px 0px; " />
				<span></span>.<br /><br clear="all" />
			</div>			
			<label for="firstname">First Name:</label><br />
			<input name="firstname" type="text" id="firstname" value=""/>
			<br />
			<label for="lastname">Last Name:</label><br />
			<input name="lastname" type="text" id="lastname" value=""/>
			<br />
			<label for="login">Login ID:</label><br />
			<input name="login" type="text" id="login" value=""/>
			<br />
			<label for="email">E-mail:</label><br />
			<input name="email" type="text" id="email" value=""/>
			<br />			
			<label for="password1">Password:</label><br />
			<input name="password1" type="password" id="password1" value=""/>
			<br />	
			<label for="password2">Password (verify):</label><br />
			<input name="password2" type="password" id="password2" value=""/>
			<br />	
			<input type="hidden" name="ref" value="add" />
		</form>	
		<div id="edituser">
			<form id="frm_edituser" action="#">
				<div class="error" style="display:none;">
					<img src="css/images/important.gif" alt="Warning!" width="24" height="24" style="float:left; margin: -5px 10px 0px 0px; " />
					<span></span>.<br /><br clear="all" />
				</div>			
				<label for="e_firstname">First Name:</label><br />
				<input name="e_firstname" type="text" id="e_firstname" value=""/>
				<br />
				<label for="e_lastname">Last Name:</label><br />
				<input name="e_lastname" type="text" id="e_lastname" value=""/>
				<br />
				<label for="e_login">Login ID:</label><br />
				<input name="e_login" type="text" id="e_login" value=""/>
				<br />
				<label for="e_email">E-mail:</label><br />
				<input name="e_email" type="text" id="e_email" value=""/>
				<br />			
				<label for="e_password1">Password:</label><br />
				<input name="e_password1" type="password" id="e_password1" value=""/>
				<br />	
				<label for="e_password2">Password (verify):</label><br />
				<input name="e_password2" type="password" id="e_password2" value=""/>
				<br />	
				<input type="hidden" name="member_id" id="member_id" value="" />
			</form>			
		</div>
		<table class="list datatable" id="user_table">
			<thead>
				<tr>
					<th></th>
					<th>Full Name</th>
					<th>Username</th>
					<th>Email</th>
				</tr>
			</thead>
			<tbody>
				<tr>
			<td colspan="9" class="dataTables_empty">Loading data from server</td>
		</tr>
			</tbody>					
		</table>
	</div>
<?php include('footer.php'); ?>

</body>
</html>
	