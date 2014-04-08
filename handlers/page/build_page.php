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
