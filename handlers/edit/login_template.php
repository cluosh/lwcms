<?php
	/*
	 * lwCMS: A lightweight CMS.
     * Copyright (C) 2014  Michael Pucher (cluosh@shadow-code.org)
     *
	 * This program is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation, either version 3 of the License, or
     * (at your option) any later version.
	 * 
     * This program is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     * GNU General Public License for more details.
	 * 
     * You should have received a copy of the GNU General Public License
     * along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */
?>
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
