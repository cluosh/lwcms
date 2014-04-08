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
					if(count($form_split) >= 3) {
						// General form data
						$name = $utility->escape($form_split[0]);
						$fields = $utility->escape(substr($form_split[1],0,-1));
						$checks = $utility->escape(substr($form_split[2],0,-1));
						array_push($forms,"INSERT INTO `".$utility->prefix()."mod_inlineforms` VALUES ('".$name."','".$fields."','".$checks."');");					
						if(count($form_split) == 4) {
							if(file_exists(dirname(__FILE__).'/../../forms/'.$form_split[0].'/dyn.php')) {
								include(dirname(__FILE__).'/../../forms/'.$form_split[0].'/dyn.php');
								$className = 'lwCMS_dyn_'.$form_split[0];
								$object = new $className($utility);
								$object->editSave($form_split[3]);
							}
						}
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
					if(file_exists(dirname(__FILE__).'/../../forms/'.$row['form'].'/dyn.php')) {
						include(dirname(__FILE__).'/../../forms/'.$row['form'].'/dyn.php');
						$className = 'lwCMS_dyn_'.$row['form'];
						$object = new $className($utility);
						$xml_data .= "<dyn>".urlencode($object->editData())."</dyn>";
						$xml_data .= (file_exists(dirname(__FILE__).'/../../forms/'.$row['form'].'/js/edit.js') ? "<dyn_javascript>".urlencode(file_get_contents(dirname(__FILE__).'/../../forms/'.$row['form'].'/js/edit.js'))."</dyn_javascript>" : "");
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
							$xml_data .= (isset($split[5]) ? "<radio_options>".urldecode($split[5])."</radio_options>" : "");
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
