<?php
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
