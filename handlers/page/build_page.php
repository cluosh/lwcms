<?php
	/*
	 * build_page.php: Creates page class and builds the page
	 */
	
	// -----------------------------------------------------------------
	// INCLUDES
	// -----------------------------------------------------------------
	
	// Include page class
	require_once(dirname(__FILE__).'/../../classes/page_handling/page.php');
	
	// -----------------------------------------------------------------
	// Build page
	// -----------------------------------------------------------------
	$pageObject = "";
	$pagemode = (isset($_GET['edit']) && $_GET['edit'] != "" ? 1 : (isset($_GET['page']) && $_GET['page'] != "" ? 0 : (isset($_GET['login']) && $_GET['login'] != "" ? 2 : (isset($_GET['logout']) && $_GET['logout'] != "" ? 3 : (isset($_GET['js']) && $_GET['js'] != "" ? 4 : 0)))));
	$page = (isset($_GET['edit']) && $_GET['edit'] != "" ? $_GET['edit'] : (isset($_GET['page']) && $_GET['page'] != "" ? $_GET['page'] : (isset($_GET['login']) && $_GET['login'] != "" ? $_GET['login'] : (isset($_GET['logout']) && $_GET['logout'] != "" ? $_GET['logout'] : (isset($_GET['js']) && $_GET['js'] != "" ? $_GET['js'] : "home")))));
	
	// Chose page display by page mode
	if($pagemode == 1 || $pagemode == 2 || $pagemode == 3 || $pagemode == 4) $pageObject = new lwCMS_Page($page,$pagemode);
	elseif($pagemode == 0) $pageObject = new lwCMS_Page($page);
	else $pageObject = new lwCMS_Page();
	
	// Display page
	$pageObject->display();
	exit;
?>
