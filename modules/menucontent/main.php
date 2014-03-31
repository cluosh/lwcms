<?php
	/*
	 * Module menucontent main
	 */
	
	// -----------------------------------------------------------------
	// INCLUDES
	// -----------------------------------------------------------------
	
	// Include module base class
	require_once(dirname(__FILE__).'/../../classes/modules/module.php');
	
	// -----------------------------------------------------------------
	// class lwCMS_menucontent: Menu-Content main class
	// -----------------------------------------------------------------
	class lwCMS_menucontent extends lwCMS_Module {
		// Construct function
		protected function construct() {
			$this->contentArea(true);
			$this->displayedName("Menu-Content");
		}
		
		// Process content
		protected function processData() {
			$menuitem_list = array();
			$menuitems = explode(";",$this->areacontent);
			foreach($menuitems as $item) {
				$item_array = explode("=",$item);
				$name = urldecode($item_array[0]);
				if(count($item_array) == 4) {
					$pagename = urldecode($item_array[1]);
					$theme = urldecode($item_array[2]);
					$template = urldecode($item_array[3]);
					if((isset($_GET['page']) && $pagename == $_GET['page']) || (isset($_GET['edit']) && $pagename == $_GET['edit'])) {
						array_push($menuitem_list,"<a href='index.php?page=".$pagename."' class='active'>".$name."</a>\n");
					} else {
						array_push($menuitem_list,"<a href='index.php?page=".$pagename."'>".$name."</a>\n");
					}
				} elseif(count($item_array) == 2) {
					$url = urldecode($item_array[1]);
					array_push($menuitem_list,"<a href='".$url."'>".$name."</a>\n");
				}
			}
			return implode("&nbsp;&nbsp;|&nbsp;&nbsp;\n",$menuitem_list);
		}
		
		// Editing header
		public function editingHeaderInfo() { 
			return "<script type='text/javascript' src='modules/menucontent/js/menu.js'></script>";
		}
		
		// Save data from edit forms
		public function editSave($utility) { 
			// Split up data in chunks and process
			$chunks = explode(";",$utility->content['content']);
			$db_array = array();
			$html_array = array();
			foreach($chunks as $chunk) {
				$data = explode("=",$chunk);
				if(count($data) == 2) {
					$pagename=$data[0];
					$name= ($data[1] == "" ? "NO_NAME" : $data[1]);
					$theme="";
					$template="";
					// Query database to get template and theme
					$result = $utility->query("SELECT `theme_name`,`template_name` FROM `".$utility->prefix()."pages` WHERE `pageID_string`='".$utility->escape($pagename)."';");
					if($result->num_rows == 1) {
						$array = $result->fetch_assoc();
						$theme = $array['theme_name'];
						$template = $array['template_name'];
					} else {
						echo "FAIL";
						exit;
					}
					// Add Info to DB-String
					array_push($db_array,$name."=".$pagename."=".$theme."=".$template);
					// Add Info to HTML-String
					array_push($html_array,"<a href='index.php?page=".$pagename."'>".urldecode($name)."</a>\n");
				} elseif(count($data) == 3) {
					$url=$data[0];
					$name= ($data[1] == "" ? "NO_NAME" : $data[1]);
					// Add Info to DB-String
					array_push($db_array,$name."=".$url);
					// Add Info to HTML-String
					array_push($html_array,"<a href='".$url."'>".$name."</a>\n");
				}
			}
			$utility->content['content'] = implode(";",$db_array);
			$utility->updateDB();
			return implode("&nbsp;&nbsp;|&nbsp;&nbsp;\n",$html_array); 
		}
		
		// Return menu overview for editing purposes
		public function editData($utility) { 
			// Query database
			$pages = $utility->query("SELECT `ID`,`pageID_string` FROM `".$utility->prefix()."pages`;");
			// Keep track of iterations
			$pages_array = array();
			while($row = $pages->fetch_assoc())
				array_push($pages_array,$row['pageID_string']);
			$data = implode("!!",$pages_array);
			$data .= "!!DATA!!";
			$menuContent = $utility->query("SELECT `content` FROM `".$utility->prefix()."pages_content` WHERE `pageID`='".$utility->escape($utility->content['pageID'])."' AND `contentArea`='".$utility->escape($utility->content['contentArea'])."';");
			if($menuContent->num_rows == 1) {
				$array = $menuContent->fetch_assoc();
				$data .= $array['content'];
			}
			return $data;
		}
	}
?>
