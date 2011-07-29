<?php

if(count($_POST) == 0) {
    Header("Location: items.php");
    die();
}
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

switch($_POST['ref']) {
	case "item":
		$id = 0;
		$i_query = "INSERT INTO item VALUES('',".$_SESSION['SESS_MEMBER_ID'].",".$_POST['manufacturer'].",'".mysql_real_escape_string(@trim($_POST['partnumber']))."',".$_POST['roadname'].",".$_POST['type'].",'".mysql_real_escape_string(@trim($_POST['description']))."','".mysql_real_escape_string(@trim($_POST['roadnumber']))."','".@trim($_POST['scale'])."',".$_POST['value'].")";
		$i_result = mysql_query($i_query) or die(mysql_error());
		$u_query = "UPDATE `update` SET update_time=".time()." WHERE member_id=".$_SESSION['SESS_MEMBER_ID'];
		$u_result = mysql_query($u_query) or die(mysql_error());
		if(mysql_affected_rows() == 0) {
			mysql_query("INSERT INTO `update` VALUES(".$_SESSION['SESS_MEMBER_ID'].", ".time().")") or die(mysql_error());
		}
		$id_query = mysql_query("SELECT MAX(i_index) FROM item WHERE i_ownerid=".$_SESSION['SESS_MEMBER_ID']) or die(mysql_error());
		$id_result = mysql_fetch_row($id_query);
		$id = $id_result[0];
		echo $id;
	break;
	case "manufacturer":
		$id = 0;
		$new_man = $_POST['manufacturer'];
		$m_query = sprintf("INSERT INTO manufacturer VALUES('','%s')",mysql_real_escape_string(@trim($new_man)));
		$m_result = mysql_query($m_query);	
		$id_query = mysql_query("SELECT MAX(m_index) FROM manufacturer") or die(mysql_error());
		$id_result = mysql_fetch_row($id_query);
		$id = $id_result[0];
		echo $id;
	break;
	case "road":
		$id = 0;	
		$new_road = $_POST['roadname'];
		$r_query = sprintf("INSERT INTO roadnames VALUES('','%s')",mysql_real_escape_string(@trim($new_road)));
		$r_result = mysql_query($r_query);	
		$id_query = mysql_query("SELECT MAX(r_index) FROM roadnames") or die(mysql_error());
		$id_result = mysql_fetch_row($id_query);
		$id = $id_result[0];
		echo $id;
	break;
	case "type":
		$id = 0;	
		$new_type = $_POST['type'];
		$t_query = sprintf("INSERT INTO type VALUES('','%s')",mysql_real_escape_string(@trim($new_type)));
		$t_result = mysql_query($t_query);	
		$id_query = mysql_query("SELECT MAX(t_index) FROM type") or die(mysql_error());
		$id_result = mysql_fetch_row($id_query);
		$id = $id_result[0];
		echo $id;
	break;
}

?>