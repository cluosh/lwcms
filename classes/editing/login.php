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
	 * login.php: General login procedure
	 */
	 
	// -----------------------------------------------------------------
	// class lwCMS_LoginHandler: General login procedure class
	// -----------------------------------------------------------------
	class lwCMS_LoginHandler {
		// Constructor, take DB connection as argument
		public function __construct($db,$prefix) {
			$this->db = $db;
			$this->prefix = $prefix;
		}
		
		// Validate Log-In
		public function checkLogin($user,$pass) {
			$user = $this->db->real_escape_string($user);
			$passhash = sha1($user.$this->db->real_escape_string($pass));
			
			// Query the database
			$results = $this->db->query("SELECT * FROM `".$this->prefix."users` WHERE `name`='".$user."' AND `passhash`='".$passhash."';");
			if($results->num_rows == 1) {
				if(session_id() == '') session_start();
				$array = $results->fetch_assoc();
				$_SESSION['editing'] = true;
				$_SESSION['level'] = $array['level'];
				return true;
			} else {
				return false;
			}
		}
		
		// Display login page
		public function displayLoginPage() {
			// Load Login-Template
			require_once(dirname(__FILE__)."/../../handlers/edit/login_template.php");
		}
		
		// Logout
		public function logOut() {
			if(session_id() == '') session_start();
			if(isset($_SESSION['editing'])) unset($_SESSION['editing']);
			if(isset($_SESSION['level'])) unset($_SESSION['level']);
		}
	}
?>
