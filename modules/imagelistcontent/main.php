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
	 * Module imagelistcontent main
	 */
	
	// -----------------------------------------------------------------
	// INCLUDES
	// -----------------------------------------------------------------
	
	// Include module base class
	require_once(dirname(__FILE__).'/../../classes/modules/module.php');
	
	// -----------------------------------------------------------------
	// class lwCMS_imagelistcontent: Imagelist-content main class
	// -----------------------------------------------------------------
	class lwCMS_imagelistcontent extends lwCMS_Module {
		// Construct function
		protected function construct() {
			$this->contentArea(true);
			$this->displayedName("Imagelist-Content");
		}
		
		// Process content
		protected function processData() {
			// Image List Items
			$imagelist = "";
			$listitems = explode(";",$this->areacontent);
			foreach($listitems as $item) {
				$content = explode("=",$item);
				if(count($content) != 4) break;
				$url = urldecode($content[0]);
				$short_text = urldecode($content[1]);
				$long_text = urldecode($content[2]);
				$popup_text = urldecode($content[3]);
				$imagelist .= "<div class='image-list-box'>";
				$imagelist .= "<img class='image' src='".$url."' alt='".$popup_text."'/>";
				$imagelist .= ($long_text != "") ? '<div class="text"><div>'.$short_text.'</div>&nbsp;&nbsp;<a href="#" class="read-more">Read more.</a></div><div class="hidden-text"><a href="#" class="image-list-hide">#</a>'.$long_text.'</div>' : '<div class="text">'.$short_text.'</div>';
				$imagelist .= "</div>\n";
			}
			return $imagelist;
		}
		
		// Define header info
		public function headerInfo() {
			return "<script type='text/javascript' src='modules/imagelistcontent/js/imagelist.js'></script>
			<link rel='stylesheet' type='text/css' href='modules/imagelistcontent/css/imagelist.css' />";
		}
		
		// Editing header
		public function editingHeaderInfo() { 
			return "<script type='text/javascript' src='modules/imagelistcontent/js/edit.js'></script>
			<link rel='stylesheet' type='text/css' href='modules/imagelistcontent/css/edit.css'/>";
		}
		
		// Save data from edit forms
		public function editSave($utility) { 
			// Split up data in chunks and process
			// Update database
			$utility->content['content'] = substr($utility->content['content'],0,-1);
			$utility->updateDB();
			$this->init($utility->content['contentArea'],$utility->content['content']);
			return $this->processData();
		}
		
		// Return menu overview for editing purposes
		public function editData($utility) { 
			// Query database
			$data = $utility->query("SELECT `content` FROM `".$utility->prefix()."pages_content` WHERE `pageID`='".$utility->escape($utility->content['pageID'])."' AND `contentArea`='".$utility->escape($utility->content['contentArea'])."' AND `contentType`='imagelistcontent';");
			if($data->num_rows == 1) {
				$array = $data->fetch_assoc();
				return $array['content'];
			}
			else 
				return "FAIL";
		}
	}
?>
