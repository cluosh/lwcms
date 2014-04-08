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
     
	/*
	 * ajax.php: Ajax interface for edit class
	 */
	
	// -----------------------------------------------------------------
	// Includes
	// -----------------------------------------------------------------
	
	// Include edit class
	require_once(dirname(__FILE__).'/../../classes/editing/edit.php');
	
	// -----------------------------------------------------------------
	// AJAX Wrapper
	// -----------------------------------------------------------------
	if(session_id() == '') session_start();
	if(!isset($_SESSION['editing'])) { echo "FAIL"; exit; }

	if(isset($_POST['pageid']) && $_POST['pageid'] != "" && isset($_POST['contentArea']) && $_POST['contentArea'] != "" && isset($_POST['contentType']) && $_POST['contentType'] != "" && isset($_POST['content'])) {
		$edit = new lwCMS_Edit($_POST['pageid'],$_POST['contentArea'],$_POST['contentType'],$_POST['content']);
		echo $edit->editData(true);
		exit;
	} elseif(isset($_GET['pageid']) && $_GET['pageid'] != "" && isset($_GET['contentArea']) && $_GET['contentArea'] != "" && isset($_GET['contentType']) && $_GET['contentType'] != "") {
		$edit = new lwCMS_Edit($_GET['pageid'],$_GET['contentArea'],$_GET['contentType']);
		echo $edit->editData(false);
		exit;
	}
	
	echo "FAIL";
	exit;
?>
