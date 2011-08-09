<?php

if (eregi("menu.php",$_SERVER['SCRIPT_NAME'])) {
    Header("Location: index.php");
    die();
}
$request = explode('/',$_SERVER['REQUEST_URI']);

switch($request[1])
{
	case "index.php";
	case "logout.php";
	case "";
		$login_out = '<a href="login.php">Login</a>';
		break;
	default:
		if(isset($_SESSION['SESS_MEMBER_ID'])) {
			$login_out = '<a class="menu" href="logout.php">Logout<!--<img src="css/images/logout.png" />--></a>';
		} else {
			$login_out = '<a class="menu" href="login.php">Login<!--<img src="css/images/login.png" />--></a>';
		}
		break;
}

if(file_exists('config.php')) {
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

	if(isset($_SESSION['SESS_MEMBER_ID'])) {
		$query = "SELECT admin FROM members WHERE member_id=".$_SESSION['SESS_MEMBER_ID'];
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		$admin_link = '';
		if($row[0] == 1) {
				$is_admin = true;
				$admin_link = '<a class="menu" href="admin.php">Admin<!--<img src="css/images/admin.png" />--></a>';
		}
	}	
}

?>
	<div id="container">
		<div id="top">
			<img src="css/images/logo.png" />
		</div>
		<div id="leftnav">
			<p>
				<a class="menu" href="items.php">Items<!--<img src="css/images/items.png" />--></a>
			</p>
			<p>
				<a class="menu" href="account.php">Account<!--<img src="css/images/account.png" />--></a>
			</p>			
			<p>
				<a class="menu" href="print.php">Print<!--<img src="css/images/print.png" />--></a>
			</p>
			<p>
				<a class="menu" href="backup.php">Backup<!--<img src="css/images/backup.png" />--></a>
			</p>			
			<p>
				<?php echo $login_out; ?>
			</p>
			<p>
				<a class="menu" href="about.php">About<!--<img src="css/images/about.png" />--></a>
			</p>
			<p>
				<?php echo $admin_link; ?>
			</p>
		</div>
