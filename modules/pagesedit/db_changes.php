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
	 * db_changes.php: Updating database with new pages information
	 */
	 
	// -----------------------------------------------------------------
	// INCLUDES
	// -----------------------------------------------------------------
	
	// Include utility
	require_once(dirname(__FILE__).'/../../classes/base/utility.php');
	
	// -----------------------------------------------------------------
	// Execution code
	// -----------------------------------------------------------------
	if(session_id() == '') session_start();
	if(!isset($_SESSION['editing'])) { echo "FAIL"; exit; }
	if(isset($_POST['action']) && isset($_POST['pageid']) && isset($_POST['content'])) {
		switch($_POST['action']) {
			case 'delete':
				$utility = new lwCMS_Utility();
				$utility->query("DELETE FROM `".$utility->prefix()."pages` WHERE `ID`='".$utility->escape($_POST['pageid'])."';");
				exit;
				break;
			case 'new':
				$utility = new lwCMS_Utility();
				$result = $utility->query("SELECT `template_name`,`theme_name` FROM `".$utility->prefix()."pages` WHERE `pageID_string`='home';");
				if($result->num_rows != 1) {
					echo "FAIL";
					exit;
				}
				$array = $result->fetch_assoc();
				$utility->query("INSERT INTO `".$utility->prefix()."pages` (`pageID_string`,`page_title`,`theme_name`,`template_name`) VALUES ('new_page','Page-Title','".$array['theme_name']."','".$array['template_name']."');");
				$result = $utility->query("SELECT `ID` FROM `".$utility->prefix()."pages` WHERE `pageID_string`='new_page';");
				if($result->num_rows != 1) {
					echo "FAIL";
					exit;
				}
				$array = $result->fetch_assoc();
				echo $array['ID'];
				exit;
				break;
			case 'change':
				$utility = new lwCMS_Utility();
				$chunks = explode("=",$_POST['content']);
				if(count($chunks) != 2) {
					echo "FAIL";
					exit;
				}
				$utility->query("UPDATE `".$utility->prefix()."pages` SET `".$utility->escape($chunks[0])."`='".($chunks[0] == 'page_title' ? urldecode($utility->escape($chunks[1])) : $utility->escape($chunks[1]))."' WHERE `ID`='".$utility->escape($_POST['pageid'])."';");
				break;
			default:
				echo "FAIL";
				exit;
				break;
		}
	} else {
		echo "FAIL";
		exit;
	}
?>
