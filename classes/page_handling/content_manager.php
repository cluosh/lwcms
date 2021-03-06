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
				$type_data = explode("=",$type);
				if(count($type_data) == 2) $this->typeinfo[$type_data[0]] = $type_data[1];
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
			if(func_num_args() == 1) {
				$id = $this->page->pageInfo();
				$result = $this->page->query("SELECT `contentArea`,`contentType`,`content` FROM `".$this->page->prefix()."pages_content` WHERE `pageID`='".$this->page->escape($id['ID'])."' AND `contentArea`='".$this->page->escape(func_get_arg(0))."';");
			} else {
				$result = $this->page->query("SELECT `contentArea`,`contentType`,`content` FROM `".$this->page->prefix()."pages_content` WHERE `pageID`='".$this->page->escape(func_get_arg(1))."' AND `contentArea`='".$this->page->escape(func_get_arg(0))."';");
			}
			if($result->num_rows == 1) 
				return $result->fetch_assoc();
			else
				return -1;
		}
		
		// Create DB entry for content area, regarding cache
		private function createContentAreaData($areaname) {
			$content = array();
			$content['contentArea'] = $areaname;
			$content['contentType'] = (isset($this->typeinfo[$areaname]) ? $this->typeinfo[$areaname] : 'default');
			$content['content'] = "";
			$id = $this->page->pageInfo();
			$this->page->query("INSERT INTO `".$this->page->prefix()."pages_content` VALUES ('".$this->page->escape($id['ID'])."','".$this->page->escape($areaname)."','".$this->page->escape($content['contentType'])."','');");
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
				// Content is empty, try to load content from homepage
				$area = $this->createContentAreaData($areaName);
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
