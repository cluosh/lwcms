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
			return "<script type='text/javascript' src='modules/pagesedit/js/edit.js'></script>
			<link rel='stylesheet' type='text'css' href='modules/pagesedit/css/edit.css' />";
		}
		
		// Save data from edit forms
		public function editSave($utility) { 
		}
		
		// Return menu overview for editing purposes
		public function editData($utility) { 
			// List themes and templates
			$themes = array_filter(glob(dirname(__FILE__).'/../../templates/*'), 'is_dir');
			// Start xml
			$xml_data = "<data><themes>";
			foreach($themes as $theme) {
				$themearray = explode("/",$theme);
				$xml_data .= "<theme name='".$themearray[count($themearray)-1]."'>";
				$templates = glob($theme.'/*.php');
				foreach($templates as $template) {
					$template = explode("/",$template);
					$template = explode(".",$template[count($template)-1]);
					$xml_data .= "<template>".$template[0]."</template>";
				}
				$xml_data .= "</theme>";
			}
			$xml_data .= "</themes>";
			// Query database
			$xml_data .= "<pages>";
			$pages = $utility->query("SELECT * FROM `".$utility->prefix()."pages`;");
			while($row = $pages->fetch_assoc()) {
				$xml_data .= "<page id='".$row['ID']."'>";
				$xml_data .= "<id_string>".$row['pageID_string']."</id_string>";
				$xml_data .= "<title>".$row['page_title']."</title>";
				$xml_data .= "<theme>".$row['theme_name']."</theme>";
				$xml_data .= "<template>".$row['template_name']."</template>";
				$xml_data .= "</page>";
			}
			$xml_data .= "</pages></data>";
			return $xml_data;
		}
	}
?>
