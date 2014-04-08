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
	 * edit.php: Contains main editing functions
	 */
	 
	// -----------------------------------------------------------------
	// INCLUDES
	// -----------------------------------------------------------------
	
	// Include utility
	require_once(dirname(__FILE__).'/../base/utility.php');
	
	// -----------------------------------------------------------------
	// class lwCMS_Edit: Main edit function declarations
	// -----------------------------------------------------------------
	class lwCMS_Edit extends lwCMS_Utility {
		// Constructor
		public function __construct() {
			parent::__construct();
			$this->content = array();
			if(func_num_args() == 4) {
				$this->content['pageID'] = func_get_arg(0);
				$this->content['contentArea'] = func_get_arg(1);
				$this->content['contentType'] = func_get_arg(2);
				$this->content['content'] = func_get_arg(3);
			} elseif(func_num_args() == 3) {
				$this->content['pageID'] = func_get_arg(0);
				$this->content['contentArea'] = func_get_arg(1);
				$this->content['contentType'] = func_get_arg(2);
			}
		}
		
		// Get/send data from module 
		public function editData($post) {
			// Check if module exists and include
			if(file_exists(dirname(__FILE__).'/../../modules/'.$this->content['contentType'].'/main.php')) {
				require_once(dirname(__FILE__).'/../../modules/'.$this->content['contentType'].'/main.php');
				$classname = "lwCMS_".$this->content['contentType'];
				$class = new $classname();
				if($post)
					return $class->editSave($this);
				else
					return $class->editData($this);
			}
			return dirname(__FILE__).'/../../modules/'.$this->content['contentType'].'/main.php'."MODULE NOT FOUND";
		}
		
		public function updateDB() {
			// Escape all strings
			$pageid = $this->escape($this->content['pageID']);
			$contentArea = $this->escape($this->content['contentArea']);
			$contentType = $this->escape($this->content['contentType']);
			$content = $this->escape($this->content['content']);
			
			// Check if entry already exists
			$result = $this->query("SELECT `contentArea` FROM `".$this->prefix()."pages_content` WHERE `pageid`='".$pageid."' AND `contentArea`='".$contentArea."';");
			if($result->num_rows == 1) {
				$this->query("UPDATE `".$this->prefix()."pages_content` SET `contentType`='".$contentType."',content='".$content."' WHERE `pageid`='".$pageid."' AND `contentArea`='".$contentArea."';");
			} else {
				$this->query("INSERT INTO `".$this->prefix()."pages_content` (`pageID`,`contentArea`,`contentType`,`content`) VALUES ('".$pageid."','".$contentArea."','".$contentType."','".$content."');");
			}
		}
	}
?>
