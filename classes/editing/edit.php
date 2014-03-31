<?php
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
