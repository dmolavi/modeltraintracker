<?php
	define('DB_HOST', '');
    define('DB_USER', '');
    define('DB_PASSWORD', '');
    define('DB_DATABASE', ''); 
	
if (eregi("config.php",$_SERVER['PHP_SELF'])) {
        Header("Location: index.php");
        die();
}	
?>