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
				$this->dyndata = urldecode(func_get_arg(1));
			} else { 
				$this->utility = func_get_arg(0);
				$this->dyndata = "";
			}
				
		}
		// Empty functions
		public function javascript_check($field) {
			return "";
		}
		public function php_check($field) {
			return "";
		}
		public function before_after() {
			return "";
		}
		public function content($field) {
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
		
		// Filter id_string
		protected function filterID($filterstring) {
			$string = urldecode($filterstring);
			$string = strip_tags($string);
			$string = preg_replace("/[^A-Za-z0-9\-_]/", '', $string);
			$string = strtolower($string);
			return $string;
		}
	}
?>
