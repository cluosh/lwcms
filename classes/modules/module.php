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
