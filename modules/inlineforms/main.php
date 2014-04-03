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
			$this->alwaysLoad(true);
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
			if($utility->content['contentArea'] == 'none') {
				$chunks = explode("!!",$utility->content['content']);
				$forms = array();
				foreach($chunks as $form) {
					$form_split = explode("==",$form);
					if(count($form_split) == 3) {
						// General form data
						$name = $utility->escape($form_split[0]);
						$fields = $utility->escape(substr($form_split[1],0,-1));
						$checks = $utility->escape(substr($form_split[2],0,-1));
						array_push($forms,"INSERT INTO `".$utility->prefix()."mod_inlineforms` VALUES ('".$name."','".$fields."','".$checks."');");					
					} 
				}
				// Delete old data from DB
				$utility->query("TRUNCATE TABLE `".$utility->prefix()."mod_inlineforms`;");
				foreach($forms as $query) $utility->query($query);
				return "";
			}
		}
		
		// Return menu overview for editing purposes
		public function editData($utility) { 
			if($utility->content['contentArea'] == 'none') {
				$xml_data = "<data>";
				// Get data from DB
				$result = $utility->query("SELECT * FROM `".$utility->prefix()."mod_inlineforms`;");
				while($row = $result->fetch_assoc()) {
					$xml_data .= "<form name='".$row['form']."'>";
					$params = explode(";",$row['checks']);
					// Preferences
					$xml_data .= "<pref>";
					foreach($params as $param) {
						$split = explode("=",$param);
						if(count($split) == 2)
							$xml_data .= "<".$split[0].">".$split[1]."</".$split[0].">";
					}
					$xml_data .= "</pref>";
					// Fields
					$xml_data .= "<fields>";
					$fields = explode(";",$row['fields']);
					foreach($fields as $field) {
						$split = explode("=",$field);
						if($split[0] != "") {
							$xml_data .= "<field name='".$split[0]."'>";
							$xml_data .= "<name>".urldecode($split[1])."</name>";
							$xml_data .= "<type>".$split[3]."</type>";
							$xml_data .= "<req>".$split[2]."</req>";
							$xml_data .= "<display_name>".$split[4]."</display_name>";
							$xml_data .= "</field>";
						}
					}
					$xml_data .= "</fields>";
					$xml_data .= "</form>";
				}
				$xml_data .= "</data>";
				return $xml_data;
			}
		}
	}
?>
