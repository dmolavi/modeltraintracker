<?php
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

switch($_POST['ref']){
	case "items":
		switch($_POST['action']) {
		case "fetch":
			$query = "SELECT i_index as i_index, i_manufacturer, i_partnumber, i_roadname, i_type, i_description, i_roadnumber, i_scale, i_value FROM item WHERE i_index=".$_POST['id'];
			$res = mysql_query($query) or die(mysql_error());
			$row = mysql_fetch_assoc($res);
			echo json_encode($row);
			break;
		case "update":
			$edit_query = "UPDATE item SET i_manufacturer=".$_POST['manufacturer'].",i_partnumber='".mysql_real_escape_string(@trim($_POST['partnumber']))."',i_roadname=".$_POST['roadname'].",i_type=".$_POST['type'].",i_description='".mysql_real_escape_string(@trim($_POST['description']))."',i_roadnumber='".mysql_real_escape_string(@trim($_POST['roadnumber']))."',i_scale='".@trim($_POST['scale'])."', i_value=".$_POST['value']." WHERE i_index=".$_POST['id'];
			$edit_result = mysql_query($edit_query);
			$u_query = "UPDATE `update` SET update_time=".time()." WHERE member_id=".$_SESSION['SESS_MEMBER_ID'];
			$u_result = mysql_query($u_query);
			break;
		}
		break;
	case "account":
		if($_POST['password']) {
			$password_string = ", passwd='".md5(mysql_real_escape_string(@trim($_POST['password'])))."'";
		} else {
			$password_string = '';
		}
		$account_query = "UPDATE members SET firstname='".mysql_real_escape_string(@trim($_POST['firstname']))."', lastname='".mysql_real_escape_string(@trim($_POST['lastname']))."', login='".mysql_real_escape_string(@trim($_POST['username']))."'".$password_string." WHERE member_id=".mysql_real_escape_string(@trim($_POST['login_id']));
		$account_result = mysql_query($account_query) or die(mysql_error());
		echo json_encode($account_result);
		break;
	case "get_all":
		$aColumns = array( 'i_index', 'm_name', 'r_roadname', 'i_roadnumber', 't_type', 'i_description', 'i_partnumber','i_value', 's_scale' );
		
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
				$sOrder = "ORDER BY m.m_name";
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

		$sQuery = "SELECT SQL_CALC_FOUND_ROWS i.i_index, m.m_name, r.r_roadname, i.i_roadnumber, t.t_type, i.i_description, i.i_partnumber, i.i_value, s.s_scale FROM item i, manufacturer m, roadnames r, type t, scale s WHERE i.i_ownerid=".$_POST['member_id']." AND i.i_manufacturer = m.m_index AND i.i_roadname = r.r_index AND i.i_type = t.t_index AND i.i_scale = s.s_id ".$sWhere." ".$sOrder." ".$sLimit;
		$rResult = mysql_query( $sQuery ) or die(mysql_error());

		$sQuery = "SELECT FOUND_ROWS()";
		$rResultFilterTotal = mysql_query( $sQuery ) or die(mysql_error());
		$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
		$iFilteredTotal = $aResultFilterTotal[0];
		
		$sQuery  = "SELECT COUNT('i_index') FROM item WHERE i_ownerid=".$_POST['member_id'];
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
}
?>