<?php

session_start();

//Check whether the session variable SESS_MEMBER_ID is present or not
if(!isset($_SESSION['SESS_MEMBER_ID']) || (trim($_SESSION['SESS_MEMBER_ID']) == '')) {
	header("location: login.php");
	exit();
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

$backupFileName = $_SESSION['SESS_FIRST_NAME']."_".$_SESSION['SESS_LAST_NAME']."_".time().".bak";

function create_backup_sql() {
	$contents = '';
	$sql_string = '';
	$tableName = "item";
	$userId = $_SESSION['SESS_MEMBER_ID'];
	$line_count = 0;
	$sql_string = NULL;
	$sql_string = "DELETE FROM $tableName WHERE i_ownerid = ".$userId.";\n";
	$table_query = mysql_query("SELECT * FROM `$tableName` WHERE i_ownerid =".$userId) or die(mysql_error());
	$num_fields = mysql_num_fields($table_query);
	while ($fetch_row = mysql_fetch_array($table_query)) {
		$sql_string .= "INSERT INTO $tableName VALUES(";
		$first = TRUE;
		for ($field_count=1;$field_count<=$num_fields;$field_count++){
		  if (TRUE == $first) {
			$sql_string .= "''";
			$first = FALSE;            
		  } else {
			$sql_string .= ", '".mysql_real_escape_string($fetch_row[($field_count - 1)])."'";
		  }
		}
		$sql_string .= ");\n";
		if ($sql_string != ""){
		  //fwrite($file,$sql_string);        
		  $contents .= $sql_string;
		}
		$sql_string = NULL;
	}    
	return $contents;
}

//$file = fopen($backupFileName, "w");
//create_backup_sql($file);
//fclose($file);
header('Content-disposition: attachment; filename='.$backupFileName);
header('Content-type: application/octet-stream');
echo create_backup_sql();

?>