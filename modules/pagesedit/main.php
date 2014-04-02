<?php
	/*
	 * Module pagesedit main
	 */
	
	// -----------------------------------------------------------------
	// INCLUDES
	// -----------------------------------------------------------------
	
	// Include module base class
	require_once(dirname(__FILE__).'/../../classes/modules/module.php');
	
	// -----------------------------------------------------------------
	// class lwCMS_pagesedit: Main class for page editing
	// -----------------------------------------------------------------
	class lwCMS_pagesedit extends lwCMS_Module {
		// Construct function
		protected function construct() {
			$this->contentArea(false);
			$this->displayedName("Page editing");
		}
		
		// Editing header
		public function editingHeaderInfo() { 
			return "<script type='text/javascript' src='modules/pagesedit/js/edit.js'></script>";
		}
		
		// Save data from edit forms
		public function editSave($utility) { 
		}
		
		// Return menu overview for editing purposes
		public function editData($utility) { 
		}
	}
?>
