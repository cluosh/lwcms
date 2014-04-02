<?php
	/*
	 * Module inlineforms main
	 */
	
	// -----------------------------------------------------------------
	// INCLUDES
	// -----------------------------------------------------------------
	
	// Include module base class
	require_once(dirname(__FILE__).'/../../classes/modules/module.php');
	
	// -----------------------------------------------------------------
	// class lwCMS_inlineforms: Inlineforms main class
	// -----------------------------------------------------------------
	class lwCMS_inlineforms extends lwCMS_Module {
		// Construct function
		protected function construct() {
			$this->contentArea(false);
			$this->displayedName("Inlineforms");
		}
		
		// Define header info
		public function headerInfo() {
			return "<script type='text/javascript' src='modules/inlineforms/js/forms.js'></script>";
		}
		
		// Editing header
		public function editingHeaderInfo() { 
			return "<script type='text/javascript' src='modules/inlineforms/js/edit.js'></script>
			<link rel='stylesheet' type='text/css' href='modules/inlineforms/css/edit.css' />";
		}
		
		// Save data from edit forms
		public function editSave($utility) { 
		}
		
		// Return menu overview for editing purposes
		public function editData($utility) { 
		}
	}
?>
