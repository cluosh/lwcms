<?php
	/*
	 * install.php: Set up tool for lwCMS
	 */
	$error_message = "";
	if(isset($_POST['submit'])) {
		if(!isset($_POST['pass']) || !isset($_POST['user']) || !isset($_POST['host']) || !isset($_POST['db']) {
			$error_message = "Enter required information";
			return;
		}
		// Connect to DB
		db = new mysqli($_POST['host'],$_POST['user'],$_POST['pass'],$_POST['db']);
		if ($db->connect_errno) {
			$error_message "Failed to connect to Database: (" . $db->connect_errno . ") " . $db->connect_error;
			return;
		}
	}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>lwCMS Setup</title>
		<style type='text/css'>
			#setup-wrapper {
				font-family:Arial, Dejavu Sans;
			}
		</style>
	</head>
	<body>
		<div id="setup-wrapper">
			<h1>lwCMS Setup</h1>
			<table>
				<tr><th>Directory</th><th>Writeable</th></tr>
				<tr><td>cache</td><td><?php echo is_writable(dirname(__FILE__).'/cache') ? "<font style='color:green;'>Yes</font>" : "<font style='color:red;'>No</font>"?></td></tr>
				<tr><td>config</td><td><?php echo is_writable(dirname(__FILE__).'/config') ? "<font style='color:green;'>Yes</font>" : "<font style='color:red;'>No</font>"?></td></tr>
				<tr><td>uploads</td><td><?php echo is_writable(dirname(__FILE__).'/cache') ? "<font style='color:green;'>Yes</font>" : "<font style='color:red;'>No</font>"?></td></tr>
			</table>
			<h2>MySQL Connection Info</h2>
			<form method="POST" action="install.php">
				<table>
					<tr><td>Database-User:</td><td><input type="text" name="user" /></td></tr>
					<tr><td>Database-Password:</td><td><input type="text" name="pass" /></td></tr>
					<tr><td>Database-Host:</td><td><input type="text" name="host" /></td></tr>
					<tr><td>Database-DB:</td><td><input type="text" name="database" /></td></tr>
					<tr><td><input type="submit" name="submit" value="OK" /></td><td></td></tr>
				</table>
			</form>
		</div>
	</body>
</html>
