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
	 * Module slideshowcontent main
	 */
	
	// -----------------------------------------------------------------
	// INCLUDES
	// -----------------------------------------------------------------
	
	// Include module base class
	require_once(dirname(__FILE__).'/../../classes/modules/module.php');
	
	// -----------------------------------------------------------------
	// class lwCMS_slideshowcontent: Slideshow-Content main class
	// -----------------------------------------------------------------
	class lwCMS_slideshowcontent extends lwCMS_Module {
		// Construct function
		protected function construct() {
			$this->contentArea(true);
			$this->displayedName("Slideshow-Content");
		}
		
		// Process content
		protected function processData() {
			// Content-Structure: width;height;urlencode(imgurl)=urlencode(alttext);urlencode(imgurl)=urlencode(alttext);...
			$slideshowcontent = explode(";",$this->areacontent,4);
			if(count($slideshowcontent) != 4) {
				return "";
			}
			
			// Get attributes
			$width = $slideshowcontent[0];
			$height = $slideshowcontent[1];
			$display_time = $slideshowcontent[2];
			
			// Get image information
			$images = explode(";",$slideshowcontent[3]);
			// Shift pagination to the right
			$slideshow = "<style>.slidesjs-pagination { left:".($width-37-17*count($images))."px; }
			#".$this->areaname."{ height:".$height."px; width:".$width."px;position:relative; }
			#".$this->areaname."-data { position:relative; display:none; }</style>";
			
			// Print handle head
			$slideshow .=  "<div id='".$this->areaname."-data'>";
			$text = "<div class='slideshow-texts'>";
			foreach($images as $image) {
				$strings = explode("=",$image);
				$imgurl = urldecode($strings[0]);
				$alttext = "";
				if(count($strings) == 2) {
					$alttext = urldecode($strings[1]);
				}
				$slideshow .= "<img src='".$imgurl."' />";
				$text .= "<div class='slideshow-text'>".$alttext."</div>";
			}
			// Print handle foot
			$slideshow .= "</div>".$text."</div>";
			
			// Print JS Code
			$slideshow .= "
			<script id='".$this->areaname."-js' type='text/javascript'>
				$(function(){
					$('#".$this->areaname."-data').slidesjs({
						width:".$width.",
						height:".$height.",
						navigation: {
							active:false
						},
						play: {
							active:false,
							interval:".$display_time.",
							auto:true
						},
						callback: {
							loaded: function(slide) {
								$('#".$this->areaname." .slideshow-texts .slideshow-text').hide();
								$('#".$this->areaname." .slideshow-texts .slideshow-text:nth-of-type('+slide+')').show();
							},
							complete: function(slide) {
								$('#".$this->areaname." .slideshow-texts .slideshow-text').hide();
								$('#".$this->areaname." .slideshow-texts .slideshow-text:nth-of-type('+slide+')').show();
							}
						}
					});
				});
			</script>
			";
			return $slideshow;
		}
		
		// Define header info
		public function headerInfo() {
			return "<link rel='stylesheet' type='text/css' href='modules/slideshowcontent/css/slides.css' />
			<script type='text/javascript' src='thirdparty/slidesjs/jquery.slides.min.js'></script>";
		}
		
		// Editing header
		public function editingHeaderInfo() { 
			return "<script type='text/javascript' src='modules/slideshowcontent/js/slides.js'></script>
			<link rel='stylesheet' type='text/css' href='modules/slideshowcontent/css/edit.css'/>";
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
			$data = $utility->query("SELECT `content` FROM `".$utility->prefix()."pages_content` WHERE `pageID`='".$utility->escape($utility->content['pageID'])."' AND `contentArea`='".$utility->escape($utility->content['contentArea'])."' AND `contentType`='slideshowcontent';");
			if($data->num_rows == 1) {
				$array = $data->fetch_assoc();
				return $array['content'];
			}
			else 
				return "FAIL";
		}
	}
?>
