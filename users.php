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
	case "add":
		$id = 0;
		$insert_query = "INSERT INTO members VALUES('','".mysql_real_escape_string(@trim($_POST['firstname']))."','".mysql_real_escape_string(@trim($_POST['lastname']))."','".mysql_real_escape_string(@trim($_POST['login']))."','".mysql_real_escape_string(@trim($_POST['email']))."','".md5(mysql_real_escape_string(@trim($_POST['password1'])))."',0)";
		$insert_result = mysql_query($insert_query) or die(mysql_error());
		
		$to = @trim($_POST['email']);
		$from = "admin@modeltraintracker.com";
		$subject = "Welcome to Model Train Tracker";
		$message = "Hello ".trim($_POST['firstname']).",\n";
		$message .= "Your login information for http://www.modeltraintracker.com/ is as follows:\n";
		$message .= "Username: ".@trim($_POST['login'])."\n";
		$message .= "Password: ".@trim($_POST['password1'])."\n";
		$message .= "\r\nIf you have any questions, please contact admin@modeltraintracker.com.";
		$headers = 'From: admin@modeltraintracker.com' . "\r\n" .
				   'Reply-To: admin@modeltraintracker.com' . "\r\n" .
				   'X-Mailer: PHP/' . phpversion();
		mail($to,$subject,$message,$headers);
		
		$id_query = mysql_query("SELECT MAX(member_id) FROM members") or die(mysql_error());
		$id_result = mysql_fetch_row($id_query);
		$id = $id_result[0];
		echo $id;		
	break;
	case "edit":
		switch($_POST['action']) {
			case "fetch":
				$query = "SELECT member_id, firstname, lastname, email, login FROM members WHERE member_id=".$_POST['id'];
				$res = mysql_query($query) or die(mysql_error());
				$row = mysql_fetch_assoc($res);
				echo json_encode($row);				
			break;
			case "edit":
			
			break;
		}
	break;
	case "delete":
		$m_delete_query = "DELETE FROM members WHERE member_id=".$_POST['id'];
		$i_delete_query = "DELETE FROM item WHERE i_ownerid =".$_POST['id'];
		if(mysql_query($m_delete_query) && mysql_query($i_delete_query)) {
			echo "ok";
		} else {
			echo json_encode(mysql_error());
		}	
	break;
	case "get_all":
		$aColumns = array( 'member_id', 'name', 'login', 'email' );
		
		/* 
		 * Paging
		 */
		$sLimit = "";
		if ( isset( $_POST['iDisplayStart'] ) && $_POST['iDisplayLength'] != '-1' ){
			$sLimit = "LIMIT ".mysql_real_escape_string( $_POST['iDisplayStart'] ).", ".
				mysql_real_escape_string( $_POST['iDisplayLength'] );
		}
		
		/*
		 * Ordering
		 */
		if ( isset( $_POST['iSortCol_0'] ) )	{
			$sOrder = "ORDER BY  ";
			for ( $i=0 ; $i<intval( $_POST['iSortingCols'] ) ; $i++ ) {
				if ( $_POST[ 'bSortable_'.intval($_POST['iSortCol_'.$i]) ] == "true" ) {
					$sOrder .= $aColumns[ intval( $_POST['iSortCol_'.$i] ) ]."
						".mysql_real_escape_string( $_POST['sSortDir_'.$i] ) .", ";
				}
			}
			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" ){
				$sOrder = "ORDER BY member_id";
			}
		}		

		/* 
		 * Filtering
		 */
		$sWhere = "";
		if ( $_POST['sSearch'] != "" ) {
			$sWhere = " AND (";
			for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
				$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_POST['sSearch'] )."%' OR ";
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
		
		/* Individual column filtering */
		for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
			if ( $_POST['bSearchable_'.$i] == "true" && $_POST['sSearch_'.$i] != '' )	{
				if ( $sWhere == "" ) {
					$sWhere = " AND ";
				} else {
					$sWhere .= " AND ";
				}
				$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_POST['sSearch_'.$i])."%' ";
			}
		}

		$sQuery = "SELECT SQL_CALC_FOUND_ROWS member_id, CONCAT(firstname,' ',lastname) AS name, login, email FROM members ".$sWhere." ".$sOrder." ".$sLimit;
		$rResult = mysql_query( $sQuery ) or die(mysql_error());

		$sQuery = "SELECT FOUND_ROWS()";
		$rResultFilterTotal = mysql_query( $sQuery ) or die(mysql_error());
		$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
		$iFilteredTotal = $aResultFilterTotal[0];
		
		$sQuery  = "SELECT COUNT('member_id') FROM members";
		$rResultTotal = mysql_query( $sQuery ) or die(mysql_error());
		$aResultTotal = mysql_fetch_array($rResultTotal);
		$iTotal = $aResultTotal[0];

		$output = array( "sEcho" => intval($_POST['sEcho']), "iTotalRecords" => $iTotal,	"iTotalDisplayRecords" => $iFilteredTotal,"aaData" => array());
		while($aRow = mysql_fetch_array($rResult)) {
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
				$row[] = $aRow[ $aColumns[$i] ];
			}				
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );
		break;		
	break;
}
?>