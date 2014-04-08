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
	 * Module htmlcontent main
	 */
	
	// -----------------------------------------------------------------
	// INCLUDES
	// -----------------------------------------------------------------
	
	// Include module base class
	require_once(dirname(__FILE__).'/../../classes/modules/module.php');
	
	// -----------------------------------------------------------------
	// class lwCMS_htmlcontent: Html-Content main class
	// -----------------------------------------------------------------
	class lwCMS_htmlcontent extends lwCMS_Module {
		// Construct function
		protected function construct() {
			$this->contentArea(true);
			$this->displayedName("HTML-Content");
		}
		
		// Process content
		protected function processData() {
			return $this->areacontent;
		}
		
		// Editing header
		public function editingHeaderInfo() { 
			return "<script type='text/javascript' src='modules/htmlcontent/js/htmlcontent.js'></script>
			<script type='text/javascript' src='thirdparty/ckeditor/ckeditor.js'></script>
			<script type='text/javascript' src='thirdparty/ckeditor/adapters/jquery.js'></script>";
		}
		
		// Save data from edit forms
		public function editSave($utility) { 
			$utility->updateDB();
			return $utility->content['content']; 
		}
	}
?>
