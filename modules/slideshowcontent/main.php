<?php
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
			/*for($i = 0;$i < count($images);$i++) {
				$strings = explode("=",$images[$i]);
				$imgurl = urldecode($strings[0]);
				$alttext = "";
				if(count($strings) == 2) {
					$alttext = urldecode($strings[1]);
				}
				$slideshow .= "<img src='".$imgurl."' />";
				$text .= "<div id='slideshow-text_".$i."' class='slideshow-text'>".$alttext."</div>";
			}*/
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
	}
?>
