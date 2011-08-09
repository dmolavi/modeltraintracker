<?php

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
					echo "<option value=\"$s_row[0]\">".$s_row[1]."</option>";
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
						echo "<option value=\"$m_row[0]\">".$m_row[1]."</option>"; 
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
						echo "<option value=\"$r_row[0]\">".$r_row[1]."</option>"; 
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
						echo "<option value=\"$t_row[0]\">".$t_row[1]."</option>"; 
					}
				?>				
			</select>&nbsp;&nbsp;<div id="addTypePopup" style="display:inline;" class="addPopup">Not listed?</div>
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
