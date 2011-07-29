<?php
if (eregi("footer.php",$_SERVER['SCRIPT_NAME'])) {
    Header("Location: index.php");
    die();
}
?>
	<div id="footer">
		&copy;2010 - <?php echo date("Y"); ?> Dariush Molavi
	</div>
</div>	