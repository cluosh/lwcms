<?php
	/*
	 * module.php: Contains module base class
	 */
	
	// -----------------------------------------------------------------
	// class lwCMS_Module: Module base class
	// -----------------------------------------------------------------
	abstract class lwCMS_Module {
		// Abstract child constructor
		abstract protected function construct();
		
		// Constructor
		public function __construct() {
			$this->header_menu_items = array();
			$this->isContentArea = false;
			$this->always_load = false;
			$this->displayed_name = "";
			$this->areaname = "";
			$this->areacontent = "";
			$this->construct();
		}
		
		// Set/Get if module contains content
		public function contentArea() {
			if(func_num_args() == 1) 
				$this->isContentArea = func_get_arg(0);
			else 
				return $this->isContentArea;
		}
		
		// Set/get if module is always loaded
		public function alwaysLoad() {
			if(func_num_args() == 1)
				$this->always_load = func_get_arg(0);
			else
				return $this->always_load;
		}
		
		// Set/get displayed name of module
		public function displayedName() {
			if(func_num_args() == 1)
				$this->displayed_name = func_get_arg(0);
			else
				return $this->displayed_name;
		}
		
		// Add header menu item
		protected function add_header_menu_item($id,$name,$href) {
			$id = count($this->header_menu_items);
			$this->header_menu_items[$id] = array();
			$this->header_menu_items[$id]['id'] = $id;
			$this->header_menu_items[$id]['name'] = $name;
			$this->header_menu_items[$id]['href'] = $href;
		}
		
		// Empty functions (Ment to be replaced by child functions
		protected function processData() { return ""; }
		public function displayData() { return $this->processData(); }
		public function editData($utility) { return ""; }
		public function editSave($utiltiy) { return ""; }
		public function headerInfo() { return ""; }
		public function editingHeaderInfo() { return ""; }
		
		// Init module with data from content_manager
		public function init($areaname,$areacontent) {
			$this->areaname = $areaname;
			$this->areacontent = $areacontent;
		}
	}
?>
