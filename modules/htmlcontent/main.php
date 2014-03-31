<?php
	/*
	 * Module htmlcontent main
	 */
	
	// -----------------------------------------------------------------
	// INCLUDES
	// -----------------------------------------------------------------
	
	// Include module base class
	require_once(dirname(__FILE__).'/../../classes/modules/module.php');
	
	// -----------------------------------------------------------------
	// class lwCMS_htmlcontent: Html-Content main class
	// -----------------------------------------------------------------
	class lwCMS_htmlcontent extends lwCMS_Module {
		// Construct function
		protected function construct() {
			$this->contentArea(true);
			$this->displayedName("HTML-Content");
		}
		
		// Process content
		protected function processData() {
			return $this->areacontent;
		}
		
		// Editing header
		public function editingHeaderInfo() { 
			return "<script type='text/javascript' src='modules/htmlcontent/js/htmlcontent.js'></script>
			<script type='text/javascript' src='thirdparty/ckeditor/ckeditor.js'></script>
			<script type='text/javascript' src='thirdparty/ckeditor/adapters/jquery.js'></script>";
		}
		
		// Save data from edit forms
		public function editSave($utility) { 
			$utility->updateDB();
			return $utility->content['content']; 
		}
	}
?>
