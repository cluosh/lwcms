<?php
	/*
	 * dyn_base.php: Base class for dynamic forms
	 */
	
	//------------------------------------------------------------------
	// lwCMS_DynBase: Base class for dynamic forms
	//------------------------------------------------------------------
	class lwCMS_DynBase {
		// If there are 2 arguments, initialize with appended data
		public function __construct() {
			if(func_num_args() == 2) {
				$this->utility = func_get_arg(0);
				$this->dyndata = func_get_arg(1);
			} else { 
				$this->utility = func_get_arg(0);
				$this->dyndata = "";
			}
				
		}
		// Empty functions
		public function javascript_check() {
			return "";
		}
		public function php_check() {
			return "";
		}
		public function before_after() {
			return "";
		}
		
		// Saving and pulling function
		public function editData($name) {
			$result = $this->utility->query("SELECT `info` FROM `".$this->utility->prefix()."mod_inlineforms_info` WHERE `form`='".$name."';");
			if($result->num_rows == 1) {
				$data = $result->fetch_assoc();
				return $data['info'];
			} else
				return urlencode("<data></data>");
		}
		
		public function editSave($data,$name) {
			$this->utility->query("REPLACE INTO `".$this->utility->prefix()."mod_inlineforms_info` VALUES ('".$name."','".$this->utility->escape($data)."');");
		}
	}
?>
