<?php
	/*
	 * module_manager.php: Create module cache, load modules
	 */
	
	// -----------------------------------------------------------------
	// class lwCMS_ModuleManager: Module cache creator and module loader
	// -----------------------------------------------------------------
	class lwCMS_ModuleManager {
		// Check if module cache is created
		private function moduleCacheExist() {
			return (file_exists($this->cmodscache) && file_exists($this->editheaderscache) && file_exists($this->loadalwayscache));
		}
		
		// Load cache info
		private function moduleCacheLoad() {
			$this->edit_headers = file_get_contents($this->editheaderscache);
			$this->load_always = explode(";",file_get_contents($this->loadalwayscache));
			$this->modules = explode(";",file_get_contents($this->cmodscache));
		}
		
		// Create cache info
		private function moduleCacheCreate() {
			// Scan modules directory
			$modules = glob($this->modulesdir.'*');
			if(!$modules) return;
			$modules = array_filter($modules, 'is_dir');
			foreach($modules as $module) {
				// Include module
				require_once($module.'/main.php');
				$module_name = explode("/",$module);
				$module_name = $module_name[count($module_name)-1];
				$class_name = 'lwCMS_'.$module_name;
				$class = new $class_name();
				if($class->contentArea()) $this->modules[count($this->modules)] = $module_name."=".urlencode($class->displayedName());
				if($class->alwaysLoad()) $this->load_always[count($this->load_always)] = $module_name;
				$this->edit_headers .= $class->editingHeaderInfo()."\n";
			}
			
			// Create files
			file_put_contents($this->cmodscache,implode(";",$this->modules));
			file_put_contents($this->loadalwayscache,implode(";",$this->load_always));
			file_put_contents($this->editheaderscache,$this->edit_headers);
		}
		
		// Load a single module
		public function loadModule($modulename) {
			// Include module
			if(!file_exists($this->modulesdir.$modulename.'/main.php')) return;
			require_once($this->modulesdir.$modulename.'/main.php');
			$class_name = 'lwCMS_'.$modulename;
			if(!isset($this->loaded_modules[$modulename])) $this->loaded_modules[$modulename] = new $class_name();
			// Add header
			$this->headers .= $this->loaded_modules[$modulename]->headerInfo();
		}
		
		// Load modules
		private function load() {
			// Load modules which need to be loaded at all times
			foreach($this->load_always as $module) $this->loadModule($module);
			// Load content modules
			$id = $this->page->pageInfo();
			if($id['ID'] == -1) {
				foreach($this->modules as $module) {
					$module = explode("=",$module);
					$this->loadModule($module[0]);
				}
			} else {
				$result = $this->page->query("SELECT `contentType` FROM `".$this->page->prefix()."pages_content` WHERE `pageID`='".$this->page->escape($id['ID'])."';");
				while($array = $result->fetch_assoc())
					$this->loadModule($array['contentType']);
			}
		}
		
		// Return headers
		public function headers() {
			return $this->headers;
		}
		
		// Return edit headers
		public function edit_headers() {
			$id = $this->page->pageInfo();
			$headers = "<link rel='stylesheet' type='text/css' href='handlers/edit/css/main.css' />";
			$headers .= "<script type='text/javascript'>var curPageID = ".$id['ID'].";</script>";
			$headers .= "<script type='text/javascript' src='index.php?js=edit_main&amp;edit_page=".(isset($_GET['edit']) && $_GET['edit'] != "" ? $_GET['edit'] : (isset($_GET['page']) && $_GET['page'] != "" ? $_GET['page'] : "home"))."'></script>";
			$headers .= $this->edit_headers;
			return $headers;
		}
		
		// Display content module
		public function displayContentModule($content_array) {
			$code = "";
			$code .= "<div id='".$content_array['contentArea']."' ".($this->page->pagemode == 1 ? "class='contentArea area_".$content_array['contentType']."'" : "").">\n";
			if(isset($this->loaded_modules[$content_array['contentType']])) {
				$this->loaded_modules[$content_array['contentType']]->init($content_array['contentArea'],$content_array['content']);
				$code .= $this->loaded_modules[$content_array['contentType']]->displayData();
			}
			$code .= "</div>";
			return $code;
		}
		
		// Display select options for content modules
		public function displayContentSelectOptions() {
			$code = "";
			foreach($this->modules as $module) {
				$module = explode("=",$module);
				$code .= "<option value=\"".$module[0]."\">".urldecode($module[1])."</option>";
			}
			return $code;
		}
		
		// Return content modules
		public function returnContentModules() {
			$rmodules = array();
			foreach($this->modules as $module) {
				$module = explode("=",$module);
				array_push($rmodules,$module[0]);
			}
			return $rmodules;
		}
			
		// Initialize module manager
		public function __construct($pageObject) {
			// Paths
			$this->cachedir = dirname(__FILE__).'/../../cache/';
			$this->modulesdir = dirname(__FILE__).'/../../modules/';
			$this->cmodscache = $this->cachedir.'cmods.cache';
			$this->editheaderscache = $this->cachedir.'mod_editheaders.cache';
			$this->loadalwayscache = $this->cachedir.'mod_loadalways.cache';
			// Parent Page
			$this->page = $pageObject;
			// Header variables
			$this->headers = "";
			$this->edit_headers = "";
			// Arrays for saving modules/module names
			$this->modules = array();
			$this->load_always = array();
			$this->loaded_modules = array();
			
			// Check if module cache exists
			($this->moduleCacheExist() ? $this->moduleCacheLoad() : $this->moduleCacheCreate());
			// Load modules
			$this->load();
		}
	}
?>
