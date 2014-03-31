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
	}
?>
