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
     
	/* PHP Script for AJAX Verifying */
	if(!isset($_POST['captcha_code']) || $_POST['captcha_code'] == "") {
		exit;
	}

	session_start();
	include_once('securimage/securimage.php');
	$securimage = new Securimage();

	if ($securimage->check($_POST['captcha_code']) == false) {
		echo "FALSE";
		$_SESSION['captcha'] = false;
	}
	else {
		echo "TRUE";
		$_SESSION['captcha'] = true;
	}

	exit;
?>
