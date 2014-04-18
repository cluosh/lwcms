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
	 * page.php: Loads pages and prints them out on the screen
	 */
	 
	// -----------------------------------------------------------------
	// INCLUDES
	// -----------------------------------------------------------------
	
	// Include utility
	require_once(dirname(__FILE__).'/../base/utility.php');
	// Include content area code
	require_once(dirname(__FILE__).'/content_manager.php');
	// Include module manager
	require_once(dirname(__FILE__).'/../modules/module_manager.php');
	// Include login utility
	require_once(dirname(__FILE__).'/../editing/login.php');
	
	// -----------------------------------------------------------------
	// class lwCMS_Page: Loading and dealing with page information
	// -----------------------------------------------------------------
	class lwCMS_Page extends lwCMS_Utility {
		// Constructor
		public function __construct() {
			// Construct utility
			parent::__construct();
			// Page variables
			$this->pageinfo = array();
			$this->pagemode = 0;
			$this->stylesheets = array();
			// Get number of arguments and decide what to do
			switch(func_num_args()) {
				case 0:
					$pageinfo_result = $this->query("SELECT * FROM `".$this->db_prefix."pages` WHERE `pageID_string` = 'home';");
					if($pageinfo_result->num_rows == 1) 
						$this->pageinfo = $pageinfo_result->fetch_assoc();
					else
						$this->error("No page with ID 'home' found in database.");
					$this->pagemode = ($this->checkLoginState() ? 1 : 0);
					break;
				case 1:
					$pageinfo_result = $this->query("SELECT * FROM `".$this->db_prefix."pages` WHERE `pageID_string` = '".$this->db->real_escape_string(func_get_arg(0))."';");
					if($pageinfo_result->num_rows == 1)
						$this->pageinfo = $pageinfo_result->fetch_assoc();
					else {
						$pageinfo_result = $this->query("SELECT * FROM `".$this->db_prefix."pages` WHERE `pageID_string` = 'home';");
						if($pageinfo_result->num_rows == 1) 
							$this->pageinfo = $pageinfo_result->fetch_assoc();
						else
							$this->error("No page with ID 'home' found in database.");
					}
					$this->pagemode = ($this->checkLoginState() ? 1 : 0);
					break;
				case 2:
					// Check if edit mode was specified
					if(func_get_arg(1) == 1 && !$this->checkLoginState()) {
						$login = new lwCMS_LoginHandler($this->db,$this->prefix());
						$login->displayLoginPage();
						exit;
					} elseif(func_get_arg(1) == 2) {
						$login = new lwCMS_LoginHandler($this->db,$this->prefix());
						if(!isset($_POST['user']) || $_POST['user'] == '' || !isset($_POST['pass']) || $_POST['pass'] == "") {
							header('Location:index.php?login='.func_get_arg(0));
							exit;
						}
						$login->checkLogin($_POST['user'],$_POST['pass']);
						header('Location:index.php?edit='.func_get_arg(0));
						exit;
					} elseif(func_get_arg(1) == 3) {
						$login = new lwCMS_LoginHandler($this->db,$this->prefix());
						$login->logOut();
						header('Location:index.php?page='.func_get_arg(0));
						exit;
					} elseif(func_get_arg(1) == 4 && $this->checkLoginState()) {
						if(func_get_arg(0) == "edit_main") {
							$this->pageinfo['ID'] = -1;
							// Init module manager
							$this->modman = new lwCMS_ModuleManager($this);
							require_once(dirname(__FILE__).'/../../handlers/edit/js/edit_main.js.php');
							exit;
						}
					}
					$pageinfo_result = $this->query("SELECT * FROM `".$this->db_prefix."pages` WHERE `pageID_string` = '".$this->db->real_escape_string(func_get_arg(0))."';");
					if($pageinfo_result->num_rows == 1)
						$this->pageinfo = $pageinfo_result->fetch_assoc();
					else {
						$pageinfo_result = $this->query("SELECT * FROM `".$this->db_prefix."pages` WHERE `pageID_string` = 'home';");
						if($pageinfo_result->num_rows == 1) 
							$this->pageinfo = $pageinfo_result->fetch_assoc();
						else
							$this->error("No page with ID 'home' found in database.");
					}
					$this->pagemode = ($this->checkLoginState() ? 1 : func_get_arg(1));
					break;
			}
			// Init module manager
			$this->modman = new lwCMS_ModuleManager($this);
			// Initialize content manager
			$this->conman = new lwCMS_ContentManager($this);
		}
		
		// -------------------------------------------------------------
		// Page handling functions
		// -------------------------------------------------------------
		
		// Return template path
		public function templatePath() {
			return "templates/".$this->pageinfo['theme_name'];
		}
		
		// Return pageinfo
		public function pageInfo() {
			return $this->pageinfo;
		}
		
		// Create list of stylesheets
		private function combineStylesheets() {
			$returncode = "";
			foreach($this->stylesheets as $stylesheet) $returncode .= "<link rel='stylesheet' type='text/css' href='".($this->templatePath().'/'.$stylesheet)."' />\n";
			return $returncode;
		}
		
		// Add stylesheet to stylesheet list
		private function addStylesheet($path) {
			$this->stylesheets[count($this->stylesheets)] = $path;
		}
		
		// Create header
		private function header() {
			// Page information & favicon
			echo "<title>".$this->pageinfo['page_title']."</title>\n\t\t";
			echo "<meta charset='UTF-8' />\n\t\t";
			echo "<meta name='viewport' content='width=device-width, maximum-scale=1.0' />";
			echo "<link rel='shortcut icon' type='image/x-icon' href='templates/".$this->pageinfo['theme_name']."/favicon.ico'/>\n\t\t";
			echo "<script src='//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js'></script>\n\t\t";
			// Add module scripts
			echo ($this->checkLoginState() ? $this->modman->edit_headers() : $this->modman->headers());
			// Add stylesheets
			echo $this->combineStylesheets();
		}
	
		// Create new content Area
		private function createContentArea($areaName) {
			$this->conman->create($areaName);
		}
		
		// Load template and display page
		public function display() {			
			// Load template
			require_once(dirname(__FILE__).'/../../templates/'.$this->pageinfo['theme_name'].'/'.$this->pageinfo['template_name'].'.php');
			// No further execution of php-code
			exit;
		}
	}
?>
