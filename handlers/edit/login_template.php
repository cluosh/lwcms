<!DOCTYPE HTML>
<html>
	<head>
		<title>Login</title>
		<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="handlers/edit/css/login.css" />
	</head>
	<body>
		<div id="login-wrapper">
			<h2>Login</h2>
			<form method="POST" action="index.php?login=<?php echo (isset($_GET['edit']) && $_GET['edit'] != "" ? $_GET['edit'] : 'home'); ?>">
				<table>
				<tr><td><label for="user">User: </label></td><td><input type="text" name="user" /></td></tr>
				<tr><td><label for="pass">Password: </label></td><td><input type="password" name="pass" /></td></tr>
				<tr><td><input type="submit" name="submit" value="Send"/></td><td></td></tr>
				</table>
			</form>
		</div>
	</body>
</html>
