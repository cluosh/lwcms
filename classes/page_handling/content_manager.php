<?php
	/*
	 * content_manager.php: Contains handling methods & classes for content
	 * management
	 */
	
	// -----------------------------------------------------------------
	// class lwCMS_ContentAreaManager: Handling & displaying content
	// -----------------------------------------------------------------
	class lwCMS_ContentManager {
		// -------------------------------------------------------------
		// Cache-Handlers
		// -------------------------------------------------------------
		
		// Check if Cache-File exists
		private function contentCacheExist() {
			return file_exists($this->type_cache);
		}
		
		// Create cache
		private function contentCacheCreate() {
			$results = $this->page->query("SELECT `contentType`,`contentArea` FROM `".$this->page->prefix()."pages_content`;");
			while($data = $results->fetch_assoc())
				if(!isset($this->typeinfo[$data['contentArea']])) 
					$this->typeinfo[$data['contentArea']] = $data['contentType'];
			file_put_contents($this->type_cache,http_build_query($this->typeinfo,'',';'));
		}
		
		// Load cache
		private function contentCacheLoad() {
			$type_info = file_get_contents($this->type_cache);
			$type_info = explode(";",$type_info);
			foreach($type_info as $type) {
				$type_data = explode(";",$type);
				if(count($type_data) == 2) $this->typeinfo[$type[0]] = $type[1];
			}
		}
		
		// Clean cache
		public function contentCacheClean() {
			unlink($this->type_cache);
		}
		
		// -------------------------------------------------------------
		// DB Handlers
		// -------------------------------------------------------------
		
		// Check DB for content area entry, if it exists, return as
		// array, if not, return -1
		private function getContentAreaData() {
			if(func_num_args() == 1)
				$result = $this->page->query("SELECT `contentArea`,`contentType`,`content` FROM `".$this->page->prefix()."pages_content` WHERE `pageID`='".$this->page->escape($this->page->pageInfo()['ID'])."' AND `contentArea`='".$this->page->escape(func_get_arg(0))."';");
			else
				$result = $this->page->query("SELECT `contentArea`,`contentType`,`content` FROM `".$this->page->prefix()."pages_content` WHERE `pageID`='".$this->page->escape(func_get_arg(1))."' AND `contentArea`='".$this->page->escape(func_get_arg(0))."';");
			if($result->num_rows == 1) 
				return $result->fetch_assoc();
			else
				return -1;
		}
		
		// Create DB entry for content area, regarding cache
		private function createContentAreaData($areaname) {
			$content = array();
			$content['contentArea'] = $areaname;
			$content['contentType'] = (isset($this->type_info[$areaname]) ? $this->type_info[$areaname] : 'default');
			$content['content'] = "";
			$this->page->query("INSERT INTO `".$this->page->prefix()."pages_content` VALUES ('".$this->page->escape($this->page->pageInfo()['ID'])."','".$this->page->escape($areaname)."','".$this->page->escape($content['contentType'])."','');");
			return $content;
		}
	
		// -------------------------------------------------------------
		// create: Initializes content areas and prints them out.
		// It takes the name of the area and the parent page object as
		// argument.
		// -------------------------------------------------------------
		public function create($areaName) {
			// Check if there is an entry in DB
			$area = $this->getContentAreaData($areaName);
			if($area == -1) {
				$area = $this->createContentArea($areaName);
				// Content is empty, try to load content from homepage
				$home_area = $this->getContentAreaData($areaName,1);
				if($home_area != -1) $area['content'] = $home_area['content'];
			} else {
				// If content is empty, try to load content from homepage
				if($area['content'] == "") {
					$home_area = $this->getContentAreaData($areaName,1);
					if($home_area != -1) $area['content'] = $home_area['content'];
				}
			}
			// Send data to module manager
			echo $this->page->modman->displayContentModule($area);
		}
		
		// -------------------------------------------------------------
		// Constructor
		// -------------------------------------------------------------
		public function __construct($page) {
			// Variables
			$this->cachedir = dirname(__FILE__).'/../../cache/';
			$this->type_cache = $this->cachedir.'area_type.cache';
			$this->typeinfo = array();
			$this->page = $page;
			// Build cache if it doesn't exist
			($this->contentCacheExist() ? $this->contentCacheLoad() : $this->contentCacheCreate());
		}
	}
?>
