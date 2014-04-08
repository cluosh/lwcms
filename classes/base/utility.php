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
	 * utility.php: Base class for several utilites used by almost all
	 * scripts
	 */

	// -----------------------------------------------------------------
	// INCLUDES
	// -----------------------------------------------------------------
	
	// Include DB-Info
	require_once(dirname(__FILE__).'/../../config/db.info.php');
	
	// -----------------------------------------------------------------
	// class lwCMS_Utility: Several utilities for other scripts
	// -----------------------------------------------------------------
	class lwCMS_Utility extends lwCMS_DB_Information {		
		// Start session and connect to DB on construct
		public function __construct() {
			if(session_id() == '') session_start();
			$this->db = new mysqli($this->db_server,$this->db_user,$this->db_pass,$this->db_db);
		}
		
		// Function for checking, if user is logged in
		protected function checkLoginState() {
			if(isset($_SESSION['editing'])) {
				return true;
			}
			return false;
		}
		
		// Recursive deletion
		protected function delRecursive($dir) {
			$files = array_diff(scandir($dir), array('.','..'));
			foreach ($files as $file) {
				(is_dir("$dir/$file")) ? delRecursive("$dir/$file") : unlink("$dir/$file");
			}
			return rmdir($dir);
		} 
		
		// Clean cache
		protected function wipeCache() {
			$cachefiles = array_filter(glob(dirname(__FILE__).'/../../cache/*'));
			foreach($cachefiles as $file) {
				(is_dir($file) ? delRecursive($file) : unlink($file));
			}
		}
		
		// Throw error
		protected function error($message) {
			echo $message;
			exit;
		}
		
		// Return DB-Prefix
		public function prefix() {
			return $this->db_prefix;
		}
		
		// Escape string
		public function escape($string) {
			return $this->db->real_escape_string($string);
		}
		
		// Shorcut for querying DB
		public function query($query) {
			return $this->db->query($query);
		}
	}
?>
