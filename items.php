<?php
/*
TO DO
1. Duplicate checking
2. Move add manufacturer/type to add item dialog box.
*/
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
	   
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
<title>Model Train Inventory</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<link type="text/css" href="css/layout.css" rel="stylesheet" />
<link type="text/css" href="css/jquery-ui-1.8.13.custom.css" rel="stylesheet" />	
<link type="text/css" href="css/jquery.alerts.css" rel="stylesheet" />	

<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.13.custom.min.js"></script>
<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/jquery.jeditable.mini.js"></script>
<script type="text/javascript" src="js/jquery.alerts.js"></script>
<script type="text/javascript" src="js/jquery.dataTables.editable.js"></script>

<script type="text/javascript"> 
	function fnFormatDetails ( oTable, nTr ) {
		var aData = oTable.fnGetData( nTr );
		if(aData[6] !== 'unk') {
			var pn = aData[6].replace(/^[ 0]/g,'');
		} else {
			var pn = '';
		}
		var sOut = '<div class="innerDetails">'+
						'<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;"  id="innerDetails">'+
							'<tr><td>Scale:</td><td>'+aData[8]+'</td></tr>'+
							'<tr><td>Manufacturer:</td><td>'+aData[1]+'</td></tr>'+
							'<tr><td>Roadname & number:</td><td>'+aData[2]+' '+ aData[3]+'</td></tr>'+
							'<tr><td>Part number:</td><td>'+aData[6]+'</td></tr>'+
							'<tr><td>Description:</td><td>'+aData[5]+'</td></tr>'+
							'<tr><td>Est. Value:</td><td> $'+aData[7]+'</td></tr>'+
							//'<tr><td>Walthers Info:</td><td><a href="http://www.walthers.com/exec/search?category=&scale=&manu='+aData[1]+'&item='+pn+'&keywords=&words=restrict&instock=Q&showdisc=Y&split=30&Submit=Search" target="new">Click here</a></td></tr>'+
						'</table>'+
					'</div>';
		return sOut;
	}

	var oTable, oSettings;
	
	$(function(){
		var clickedRowId, oldRow, edit_errors, nTr;
		var anOpen = [];
		var sImageUrl = "css/images/";
		var sSource = "edit.php";
		var partnumbercbx = $("#partnumbercbx");
		var roadnumbercbx = $("#roadnumbercbx");
		var partnumber = $("#partnumber");
		var roadnumber = $("#roadnumber");
		var ed_partnumbercbx = $("#ed_partnumbercbx");
		var ed_roadnumbercbx = $("#ed_roadnumbercbx");
		var ed_partnumber = $("#ed_partnumber");
		var ed_roadnumber = $("#ed_roadnumber");
		var member_id = '<?= $_SESSION['SESS_MEMBER_ID'] ?>';
		
		$('input:submit,button').button(); 
				
		oTable = $('#item_table').dataTable({
			"bFilter": true,
			"bJQueryUI": true,
			"iDisplayLength": 25,
			"sPaginationType": "full_numbers",
			"sDom": '<"H"lfr>t<"F"ip>',
			"bSort": true,
			"bProcessing": true,
			"bServerSide":true,
			"sAjaxSource": "edit.php",
			"fnServerData": function ( sSource, aoData, fnCallback ) {
						aoData.push( { "name": "member_id", "value": member_id } );
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
			"oLanguage": {
				"sSearch": "Search:" 
			},
			"aoColumns": [
			{ "bSortable": false, "sClass": "control center read_only", "mDataProp": null, "sDefaultContent":'<img src="'+sImageUrl+'details_open.png'+'">' },
            { "bSearchable": true, "bSortable": true, "sClass": "read_only" },
			{ "bSearchable": true, "bSortable": true, "sClass": "read_only" },
			{ "bSearchable": true, "bSortable": false, "sClass": "read_only" },
			{ "bSearchable": true, "bSortable": true, "sClass": "read_only" },
			{ "bSearchable": true, "bSortable": false, "sClass": "read_only" },
			{ "bSearchable": false, "bSortable": false, "bVisible":false, "sClass": "read_only" },
			{ "bSearchable": false, "bSortable": false, "bVisible":false, "sClass": "read_only" },
			{ "bSearchable": false, "bSortable": false, "bVisible":false, "sClass": "read_only" },
			]			
		}).makeEditable({
		   	sDeleteURL: "delete.php",
			oDeleteParameters: {  ref: "items" },
			sAddURL: "add.php",
			oAddNewRowButtonOptions: {	label: "Add new item",
										icons: {primary:'ui-icon-plus'}},
			oDeleteRowButtonOptions: {	label: "Remove selected item", 
										icons: {primary:'ui-icon-trash'}},
			oAddNewRowFormOptions: {	title: 'Add a new item',
										width: 500,
										modal: true},
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
				jConfirm('Please confirm that you want to delete this item entry?', 'Confirm Delete', function (r) {
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
		}).fnFilterOnReturn();	
		
		$('#innerDetails').live('click',function() {
			return false;
		});
		
       $('#item_table td.control').live( 'click', function () {
			oldRow = nTr;
			if(oldRow) {
				$(oldRow).removeClass('row_selected'); 
				$('div.innerDetails', oldRow[0]).slideUp( function () {
						oldRow.childNodes[0].innerHTML = '<img src="'+sImageUrl+'details_open.png">';
						oTable.fnClose( oldRow );
						anOpen.shift();
				});
			}
			nTr = this.parentNode;
			var i = $.inArray( nTr, anOpen );
			
			if(nTr !== oldRow || i === -1) {
				$('img', this).attr( 'src', sImageUrl+"details_close.png" );
				var nDetailsRow = oTable.fnOpen( nTr, fnFormatDetails(oTable, nTr), 'details' );
				$('div.innerDetails', nDetailsRow).slideDown();
				$(nTr).addClass('row_selected');
				anOpen.push( nTr );
				clickedRowId = $(nTr).attr('id');
				$('#btnEditRow').button( "option", "disabled", false );
			} else {
				$('img', this).attr( 'src', sImageUrl+"details_open.png" );
				$('div.innerDetails', $(nTr).next()[0]).slideUp( function () {
					$(nTr).removeClass('row_selected');
					clickedRowId = 0;
					anOpen.shift();
					oTable.fnClose( nTr );
					$('#btnEditRow').button( "option", "disabled", true );
				  } );
			}
			return false;
		} );	
		
		$("#item_table tr.even,tr.odd").live('click',function () {
			if ($(this).hasClass("row_selected") && $(this).attr('id') !== clickedRowId) {
				$('#btnEditRow').button( "option", "disabled", false );
				clickedRowId = $(this).attr('id');
				$(anOpen).each( function() {
					var tempRow = this;
					$(tempRow).removeClass('row_selected'); 
					$('div.innerDetails', tempRow[0]).slideUp( function () {
							tempRow.childNodes[0].innerHTML = '<img src="'+sImageUrl+'details_open.png">';
							oTable.fnClose( tempRow );
							anOpen.shift();
					});
				});
			} else if($(this).attr('id') === clickedRowId) {
				$('#btnEditRow').button( "option", "disabled", true );
				$(this).removeClass("row_selected");
				var tempRow = this;
				$('div.innerDetails', tempRow[0]).slideUp( function () {
					tempRow.childNodes[0].innerHTML = '<img src="'+sImageUrl+'details_open.png">';
					oTable.fnClose( tempRow );
					anOpen.shift();
				});				
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
							$( "#edit-form-div" ).dialog( "open" );
							$.ajax({
								type: "POST",
								dataType: "json",
								data: ({id : clickedRowId, ref : "items", action : "fetch" }),
								url: "edit.php", 
								context: document.body,
								success: function(data) {
									$("#ed_description").val(data.i_description);
									$("#ed_scale").val(data.i_scale);
									if(data.i_partnumber == "unk") {
										$("#ed_partnumbercbx").attr('checked','checked');
										$("#ed_partnumber").val("");
										$("#ed_partnumber").attr("disabled", true);
									} else {
										$("#ed_partnumber").val(data.i_partnumber);
										ed_partnumber.attr("disabled", false);
									}
									if(data.i_roadnumber == "n/a") {
										$("#ed_roadnumbercbx").attr('checked','checked');
										$("#ed_roadnumber").val("");
										$("#ed_roadnumber").attr("disabled", true);
									} else {
										$("#ed_roadnumber").val(data.i_roadnumber);
									}
									$("#ed_manufacturer").val(data.i_manufacturer);					
									$("#ed_roadname").val(data.i_roadname);
									$("#ed_value").val(data.i_value);
									$("#ed_type").val(data.i_type);
									$("#item_id").val(data.i_index);
								}
							});
						});		
		partnumbercbx.click(function() {
			partnumber.val("");
			partnumber.attr("disabled", this.checked);
			partnumber.is(":disabled") ? partnumber.removeClass("error") : '';
		});
		roadnumbercbx.click(function() {
			roadnumber.attr("disabled", this.checked);
			roadnumber.val("");
			roadnumber.is(":disabled") ? roadnumber.removeClass("error") : '';
		});		
		ed_partnumbercbx.click(function() {
			ed_partnumber.attr("disabled", this.checked);
			ed_partnumber.val("");
			ed_partnumber.is(":disabled") ? ed_partnumber.removeClass("error") : '';
		});
		ed_roadnumbercbx.click(function() {
			ed_roadnumber.attr("disabled", this.checked);
			ed_roadnumber.val("");
			ed_roadnumber.is(":disabled") ? ed_roadnumber.removeClass("error") : '';
		});	
		jQuery.validator.messages.required = "";
		$("#edit-form").validate({
						rules: {
							ed_scale: "required",
							ed_manufacturer: "required",
							ed_roadname: "required",
							ed_type: "required",
							ed_description: "required",
							ed_value: "required",
							ed_partnumber: { required: "#ed_partnumbercbx:unchecked" },
							ed_roadnumber: { required: "#ed_roadnumbercbx:unchecked" }
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
								$("div.edit_error span").html(message);
								$("div.edit_error").show();
							} else {
								$("div.edit_error").hide();
							}
						}, 
						submitHandler: function(form) {
											var partnumber_ed, roadnumber_ed;
											$("#ed_roadnumbercbx").is(":checked") ? roadnumber_ed = "n/a" : roadnumber_ed = ed_roadnumber.val();
											$("#ed_partnumbercbx").is(":checked") ? partnumber_ed = "unk" : partnumber_ed = ed_partnumber.val();
											$.ajax({
												type: "POST",
												url: "edit.php",
												context: document.body,
												data:({id : $("#item_id").val(),ed_description : $("#ed_description").val(),ed_scale : $("#ed_scale").val(), ed_manufacturer: $("#ed_manufacturer").val(), ed_roadname:$("#ed_roadname").val(), ed_value : $("#ed_value").val(), ed_type : $("#ed_type").val(), ed_partnumber : partnumber_ed, ed_roadnumber : roadnumber_ed, ref : "items", action : "update" }),
												success: function(){
													oTable.fnDraw();
												}
											});							
						}
		});	
		$("#edit-form-div").dialog({
			title: "Edit item",
			autoOpen: false,
			width: 500,
			modal: true,
			buttons: {
				"Edit Item": function() {
					if($("#edit-form").valid()) {
						$("#edit-form").submit();
						$( this ).dialog( "close" );
					}
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				$(':text',"#edit-form").val("");
				ed_partnumber.attr("disabled", false);
				ed_roadnumber.attr("disabled", false);
				$(':input',"#edit-form").removeAttr('checked').removeAttr('selected');
			}
		});			

		$("#formAddNewRow").validate({
				rules: {
					scale: "required",
					manufacturer: "required",
					roadname: "required",
					type: "required",
					description: "required",
					value: "required",
					partnumber: { required: "#partnumbercbx:unchecked" },
					roadnumber: { required: "#roadnumbercbx:unchecked" }
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
		 
		$('#btnSave').button({icons: {primary:'ui-icon-disk'},
						 disabled: false
						}).click(function() {
							oSettings = oTable.fnSettings();
							var searchData = [];
							var aCols = [];
							var iCols = oSettings.aoColumns.length;
							
							for (i=0; i< iCols; i++) {
							  aCols.push(oSettings.aoColumns[i].sName);
							  searchData.push( { "name": "bSearchable_"+i, "value": oSettings.aoColumns[i].bSearchable } );
							  searchData.push( { "name": "bSortable_"+i,  "value": oSettings.aoColumns[i].bSortable } );
							}
							var sCols = aCols.join(',');
							var sSrch = $('.dataTables_filter input').val();

							if ( oSettings.oFeatures.bSort !== false )
							{
								var iFixed = oSettings.aaSortingFixed !== null ? oSettings.aaSortingFixed.length : 0;
								var iUser = oSettings.aaSorting.length;
								searchData.push( { "name": "iSortingCols",   "value": iFixed+iUser } );
								for ( i=0 ; i<iFixed ; i++ ) {
									searchData.push( { "name": "iSortCol_"+i,  "value": oSettings.aaSortingFixed[i][0] } );
									searchData.push( { "name": "sSortDir_"+i,  "value": oSettings.aaSortingFixed[i][1] } );
								}
								
								for ( i=0 ; i<iUser ; i++ )	{
									searchData.push( { "name": "iSortCol_"+(i+iFixed),  "value": oSettings.aaSorting[i][0] } );
									searchData.push( { "name": "sSortDir_"+(i+iFixed),  "value": oSettings.aaSorting[i][1] } );
								}
							}							
							
							searchData.push({"name":"sColumns","value":sCols});
							searchData.push({"name":"iColumns","value":iCols});
							searchData.push({"name":"sSearch","value":sSrch});
							searchData.push({"name":"iDisplayStart","value":0});
							searchData.push({"name":"member_id","value":<?= $_SESSION['SESS_MEMBER_ID'] ?>});

							$.ajax({
							  url: "item_print.php",
							  type: "POST", 		  
							  data: searchData,
							  success: function(data, textStatus, jqXHR) {
								window.location = data;
								$.ajax({
									url: "delete.php",
									type: "POST",
									data: ({filename: $.trim(data), ref: "del_file" })
								});
							  }
							});						
		});	

		$('#addManPopup,#addRoadPopup').click(function(){
			$('#dialog-add-MR').dialog("open");
			return false;
		}).hover(function() {
			 $(this).css('cursor','pointer');
			 }, function() {
			 $(this).css('cursor','auto');
		});	
		
		$('#dialog-add-MR').dialog({
			autoOpen: false,
			width: 500,
			modal: true,
			buttons: {
				"Add": function() {
					if($("#edit-form").valid()) {
						$("#edit-form").submit();
						$( this ).dialog( "close" );
					}
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {

			}			
		});
		
		$("input@[name='selector']").change(function(){
			if ($("input[@name='selector']:checked").val() == 'manufacturer') {
				$("#togglediv").html("New Manufacturer: ");
			} else if ($("input[@name='selector']:checked").val() == 'roadname') {
				$("#togglediv").html("New Roadname: ");
			}
		});
	});

	$.fn.dataTableExt.oApi.fnFilterOnReturn = function (oSettings) {
		/*
		* Usage:       $('#example').dataTable().fnFilterOnReturn();
		* Author:      Jon Ranes (www.mvccms.com)
		* License:     GPL v2 or BSD 3 point style
		* Contact:     jranes /AT\ mvccms.com
		*/
		var _that = this;

		this.each(function (i) {
			$.fn.dataTableExt.iApiIndex = i;
			var $this = this;
			var anControl = $('input', _that.fnSettings().aanFeatures.f);
			anControl.unbind('keyup').bind('keypress', function (e) {
				if (e.which == 13) {
					$.fn.dataTableExt.iApiIndex = i;
					_that.fnFilter(anControl.val());
				}
			});
			return this;
		});
		return this;
	}
</script>
<style type="text/css">
	.css_right {float:right}
	div.innerDetails { display: none }	
	div.addPopup { color: #0000ff; text-decoration: underline; }
</style>
</head>
<body>
<?php
include('menu.php');
?>
	<div id="content">
		<button id="btnAddNewRow">Add</button>&nbsp;<button id="btnEditRow">Edit Selected item</button>&nbsp;<button id="btnSave">Save current view</button><button id="btnDeleteRow" style="float:right">Delete</button><br /><br />
		<div id="processing_message" style="display:none" title="Processing">Please wait while your request is being processed...</div>
		<form id="formAddNewRow" action="#">
			<br />		
			<div class="error" style="display:none;">
			  <img src="css/images/important.gif" alt="Warning!" width="24" height="24" style="float:left; margin: -5px 10px 0px 0px; " />
			  <span></span>.<br clear="all" />
			</div>			
			<label for="scale">Scale:</label><br />
			<select name="scale" id="scale">
				<option value="" />
			<?php
				$s_result = mysql_query('SELECT s_id, s_scale FROM scale ORDER BY s_scale');
				while($s_row = mysql_fetch_row($s_result)) {
					echo "<option value=\"$s_row[0]\" />".$s_row[1];
				}
			?>
			</select>
			<br />
			<label for="manufacturer">Manufacturer:</label><br />
			<select name="manufacturer" id="manufacturer">
				<option value="" />
				<?php
					$m_result = mysql_query('SELECT m_index, m_name FROM manufacturer ORDER BY m_name');
					while($m_row = mysql_fetch_row($m_result)) {
						echo "<option value=\"$m_row[0]\" />";
						echo $m_row[1]; 
					}
				?>
			</select>&nbsp;&nbsp;<div id="addManPopup" style="display:inline;" class="addPopup">Not listed?</div>
			<br />
			<label for="partnumber">Part Number:</label><br />
			<input name="partnumber" type="text" id="partnumber" value=""/> or <input name="partnumber" id="partnumbercbx" type="checkbox" value="unk" />Unknown
			<br />
			<label for="roadname">Road Name:</label><br />
			<select name="roadname" id="roadname">
				<option value="" />
				<?php
					$r_result = mysql_query('SELECT r_index, r_roadname FROM roadnames ORDER BY r_roadname');
					while($r_row = mysql_fetch_row($r_result)) {
						echo "<option value=\"$r_row[0]\" />";
						echo $r_row[1]; 
					}
				?>				
			</select>&nbsp;&nbsp;<div id="addRoadPopup" style="display:inline;" class="addPopup">Not listed?</div>
			<br />
			<label for="type">Type:</label><br />
			<select name="type" id="type">
				<option value="" />
				<?php
					$t_result = mysql_query('SELECT t_index, t_type FROM type ORDER BY t_type');
					while($t_row = mysql_fetch_row($t_result)) {
						echo "<option value=\"$t_row[0]\" />";
						echo $t_row[1]; 
					}
				?>				
			</select>
			<br />
			<label for="roadnumber">Road Number:</label><br />
			<input name="roadnumber" type="text" id="roadnumber" value=""/> or <input name="roadnumber" type="checkbox" value="n/a" id="roadnumbercbx"  />N/A
			<br />
			<label for="description">Description:</label><br />
			<input name="description" type="text" id="description" value="" size="30" maxlength="50" />
			<br />
			<label for="value">MSRP / current value (nearest dollar):</label><br />
			<input name="value" type="text" id="value" value="" size="10" />
			<br />
			<input type="hidden" name="ref" value="item" />
		</form>	
		<div id="edit-form-div">
			<form id="edit-form" action="#">
				<br />		
				<div class="edit_error" style="display:none;">
				  <img src="css/images/important.gif" alt="Warning!" width="24" height="24" style="float:left; margin: -5px 10px 0px 0px; " />
				  <span></span>.<br clear="all" />
				</div>				
				<label for="ed_scale">Scale:</label><br />
				<select name="ed_scale" id="ed_scale">
					<option value="" />
				<?php
					$s_result = mysql_query('SELECT s_id, s_scale FROM scale ORDER BY s_scale');
					while($s_row = mysql_fetch_row($s_result)) {
						echo "<option value=\"$s_row[0]\" />".$s_row[1];
					}
				?>
				</select>
				<br />
				<label for="ed_manufacturer">Manufacturer:</label><br />
				<select name="ed_manufacturer" id="ed_manufacturer">
					<option value="" />
					<?php
						$m_result = mysql_query('SELECT m_index, m_name FROM manufacturer ORDER BY m_name');
						while($m_row = mysql_fetch_row($m_result)) {
							echo "<option value=\"$m_row[0]\" />";
							echo $m_row[1]; 
						}
					?>
				</select>
				<br />
				<label for="ed_partnumber">Part Number:</label><br />
				<input name="ed_partnumber" type="text" id="ed_partnumber" value=""/> or <input name="ed_partnumber" id="ed_partnumbercbx" type="checkbox" value="unk"  />Unknown
				<br />
				<label for="ed_roadname">Road Name:</label><br />
				<select name="ed_roadname" id="ed_roadname">
					<option value="" />
					<?php
						$r_result = mysql_query('SELECT r_index, r_roadname FROM roadnames ORDER BY r_roadname');
						while($r_row = mysql_fetch_row($r_result)) {
							echo "<option value=\"$r_row[0]\" />";
							echo $r_row[1]; 
						}
					?>				
				</select>
				<br />
				<label for="ed_type">Type:</label><br />
				<select name="ed_type" id="ed_type">
					<option value="" />
					<?php
						$t_result = mysql_query('SELECT t_index, t_type FROM type ORDER BY t_type');
						while($t_row = mysql_fetch_row($t_result)) {
							echo "<option value=\"$t_row[0]\" />";
							echo $t_row[1]; 
						}
					?>				
				</select>
				<br />
				<label for="ed_roadnumber">Road Number:</label><br />
				<input name="ed_roadnumber" type="text" id="ed_roadnumber" value=""/> or <input name="ed_roadnumber" id="ed_roadnumbercbx" type="checkbox" value="n/a" />N/A
				<br />
				<label for="ed_description">Description:</label><br />
				<input name="ed_description" type="text" id="ed_description" value="" size="30" maxlength="50" />
				<br />
				<label for="ed_value">MSRP / current value (nearest dollar):</label><br />
				<input name="ed_value" type="text" id="ed_value" value="" size="10" />
				<br />
				<input type="hidden" name="ref" value="edit-item" />
				<input type="hidden" name="item_id" id="item_id" value="" />
			</form>	
		</div>		
		<div id="dialog-add-MR">
			<form id="addMRForm" action="#">
				Are you adding a 
				<input type="radio" name="selector" id="selector" value="manufacturer" />Manufacturer or <input type="radio" name="selector" id="selector" value="roadname" />Roadname? <br /><br />
				<div id="togglediv" style="display:inline;"></div> <input type="text" name="newentry" id="newentry" value="" size="30" maxlength="50" />
			</form>
		</div>		
		
		<table class="list datatable" id="item_table">
			<thead>
				<tr>
					<th></th>
					<th>Manufacturer</th>
					<th>Roadname</th>
					<th>Road No.</th>
					<th>Type</th>
					<th>Description</th>
					<th></th>
					<th></th>
					<th></th>
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