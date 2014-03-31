<?php
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
