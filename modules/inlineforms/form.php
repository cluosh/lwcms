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
	 * form.php: Return or validate an inlineform
	 */
	 
	// -----------------------------------------------------------------
	// INCLUDES
	// -----------------------------------------------------------------
	
	// Include utility
	require_once(dirname(__FILE__).'/../../classes/base/utility.php');
	
	// Include dynamic forms base class
	require_once(dirname(__FILE__).'/dyn_base.php');
	
	// -----------------------------------------------------------------
	// Execution code
	// -----------------------------------------------------------------
	if(session_id() == '') session_start();
	if(!isset($_SESSION['editing'])) { echo "FAIL"; exit; }
	if(isset($_GET['form']) && isset($_GET['dyn'])) {
		$utility = new lwCMS_Utility();
		$result = $utility->query("SELECT * FROM `".$utility->prefix()."mod_inlineforms` WHERE `form`='".$utility->escape($_GET['form'])."';");
		if($result->num_rows == 1) {
			$data = $result->fetch_assoc();
			$xml_data = "<form>";
			$params = explode(";",$data['checks']);
			$captcha = false;
			$name = $utility->escape($_GET['form']);
			// Preferences
			$xml_data .= "<pref>";
			foreach($params as $param) {
				$split = explode("=",$param);
				if(count($split) == 2) {
					$xml_data .= "<".$split[0].">".$split[1]."</".$split[0].">";
					if($split[0] == 'captcha' && $split[1] == '1')
						$captcha = true;
				}
			}
			if(file_exists(dirname(__FILE__).'/../../forms/'.$name.'/dyn.php')) {
				if(!class_exists('lwCMS_dyn_'.$name)) if(!class_exists('lwCMS_dyn_'.$name)) include(dirname(__FILE__).'/../../forms/'.$name.'/dyn.php');
				$className = 'lwCMS_dyn_'.$name;
				$object = new $className($utility,$_GET['dyn']);
				$xml_data .= $object->before_after();
			}
			$xml_data .= "</pref>";
			// Fields
			$xml_data .= "<fields>";
			$fields = explode(";",$data['fields']);
			$java = "";
			$jquery_code = "";
			foreach($fields as $field) {
				$split = explode("=",$field);
				if($split[0] != "") {
					$xml_data .= "<field name='".$split[0]."'>";
					$xml_data .= "<name>".urlencode(preg_replace("/<\/?div[^>]*\>/i", "", urldecode($split[1])))."</name>";
					$xml_data .= "<type>".$split[3]."</type>";
					$xml_data .= "<req>".$split[2]."</req>";
					$xml_data .= "<display_name>".$split[4]."</display_name>";
					// Split tags from name
					$split[1] = strip_tags(urldecode($split[1]));
					if(isset($split[5])) {
						$xml_data .= "<radio_options>";
						$array = urldecode($split[5]);
						$array = explode(";;",$array);
						foreach($array as $button) 
							$xml_data .= "<button>".$button."</button>";
						$xml_data .= "</radio_options>";
					}
					if($split[3] == 'dynamic') {
						if(file_exists(dirname(__FILE__).'/../../forms/'.$name.'/dyn.php')) {
							if(!class_exists('lwCMS_dyn_'.$name)) include(dirname(__FILE__).'/../../forms/'.$name.'/dyn.php');
							$className = 'lwCMS_dyn_'.$name;
							$object = new $className($utility,$_GET['dyn']);
							$xml_data .= "<content>".$object->content($split[0])."</content>";
						}
					}
					$xml_data .= "</field>";
					if(($split[3] == 'text' || $split[3] == 'email' || $split[3] == 'password' || $split[3] == 'textarea') && $split[2] == 1) {
						$java .= "
						if(/^\s*$/.test($('form.inlineform input[name=form_".$split[0]."]').val()))
						{
							alert('".urldecode($split[1])." is a required field.');
							$('form.inlineform input[name=form_".$split[0]."]').focus();
							return false;
						}
						";
						if($split[3] == 'email') {
							$java .= "
							if(!validateEmail($('form.inlineform input[name=form_".$split[0]."]').val()))
							{
								alert($('form.inlineform input[name=form_".$split[0]."]').val());
								alert('An invalid E-Mail Address has been specified.');
								$('form.inlineform input[name=form_".$split[0]."]').focus();
								return false;
							}
							";
						}
					}
					if($split[3] == 'radio' && $split[2] == 1) {
						$java .= "
						if (!$('form.inlineform input[name=\"form_".$split[0]."\"]:checked').val()) {
							alert('".urldecode($split[1])." is a required field.');
							return false;
						}";
					} elseif($split[3] == 'dynamic' && $split[2] == 1) {
						if(!class_exists('lwCMS_dyn_'.$name)) include(dirname(__FILE__).'/../../forms/'.$name.'/dyn.php');
						$className = 'lwCMS_dyn_'.$name;
						$object = new $className($utility,$_GET['dyn']);
						$java .= $object->javascript_check($split);
					} elseif($split[3] == 'payment') {
						if($split[2] == 1) {
							$java .= "
							if (!$('form.inlineform input[name=form_".$split[0]."]:checked').val()) {
							alert('".urldecode($split[1])." is a required field.');
							return false;
							}
							if($('form.inlineform input[name=form_".$split[0]."]:checked').val() != 'bank')
							{
								if(/^\s*$/.test($('form.inlineform input[name=form_".$split[0]."_card_no]').val()))
								{
									alert('Please enter the Credit Card No.');
									$('form.inlineform input[name=form_".$split[0]."_card_no]').focus();
									return false;
								}
								if(/^\s*$/.test($('form.inlineform input[name=form_".$split[0]."_card_holder]').val()))
								{
									alert('Please enter the Card Holder Name.');
									$('form.inlineform input[name=form_".$split[0]."_card_holder]').focus();
									return false;
								}
								if(/^\s*$/.test($('form.inlineform input[name=form_".$split[0]."_expire_date]').val()))
								{
									alert('Please enter the Card\'s Expiry Date.');
									$('form.inlineform input[name=form_".$split[0]."_card_holder]').focus();
									return false;
								}
							}";
						}
						$jquery_code .= "$(document).on('click','.credit-card,.bank-transfer',function(){
							if($(this).hasClass('credit-card')) {
								$(this).siblings('.payment-info').show();
							} else {
								$(this).siblings('.payment-info').hide();
							}
						});";
					}
				}
			}
			$xml_data .= "</fields>";
			// Javascript checks
			if($captcha) {
				$java .= "
				if(check_captcha() == false) {
					alert('Image verification failed. Please try again.')
					$('form.inlineform input[name=captcha_code]').focus();
					return false;
				}
				";
			}
			$xml_data .= "<javascript>";
			$xml_data .= urlencode("function form_checks() {".$java." return true;}\n".$jquery_code);
			$xml_data .= "</javascript>";
			$xml_data .= "</form>";
			echo $xml_data;
			exit;
		} else {
			echo "FAIL";
			exit;
		}
	} elseif(isset($_POST['formID'])) {
		// Get form ID and dyndata
		$forminfo = explode("?",$_POST['formID']);
		$name = $forminfo[0];
		$dyndata = (count($forminfo) == 2 ? $forminfo[1] : "");
		// Get DB Information
		$utility = new lwCMS_Utility();
		$result = $utility->query("SELECT * FROM `".$utility->prefix()."mod_inlineforms` WHERE `form`='".$utility->escape($name)."';");
		if($result->num_rows == 1) {
			$data = $result->fetch_assoc();
			$params = explode(";",$data['checks']);
			$save_to_db = 0;
			$emails = array();
			$captcha = 0;
			$success_text = "";
			// Check preferences
			foreach($params as $param) {
				$split = explode("=",$param);
				if(count($split) == 2) {
					switch($split[0]) {
						case 'db':
							$save_to_db = $split[1];
							break;
						case 'email':
							$emails = explode(";",urldecode($split[1]));
							break;
						case 'captcha':
							$captcha = $split[1];
							break;
						case 'success_text':
							$success_text = $split[1];
							breaK;
					}
				}
			}
			// Check fields
			$fields = explode(";",$data['fields']);
			$data = "";
			foreach($fields as $field) {
				$split = explode("=",$field);
				// Strip tags from name
				$split[1] = strip_tags(urldecode($split[1]));
				if($split[0] != "") {
					if((!isset($_POST['form_'.$split[0]]) || trim($_POST['form_'.$split[0]]) == "") && $split[2] == "1" && $split[3] != 'dynamic') {
						echo "<data><error>".$split[1]." is a required field.</error></data>";
						exit;
					}
					if($split[3] == 'dynamic') {
						if(!class_exists('lwCMS_dyn_'.$name)) include(dirname(__FILE__).'/../../forms/'.$name.'/dyn.php');
						$className = 'lwCMS_dyn_'.$name;
						$object = new $className($utility,(isset($_POST['dyn']) ? $_POST['dyn'] : ""));
						$data .= $object->php_check($split);
					} elseif(isset($_POST['form_'.$split[0]]) && !($split[3] == 'radio' && $_POST['form_'.$split[0]] == 'on')) 
						$data .= $split[1].": ".$_POST['form_'.$split[0]]."\n";
					if($split[3] == 'payment' && $split[2] == "1" && $_POST['form_'.$split[0]] != 'bank' && (!isset($_POST['form_'.$split[0].'_card_no']) || trim($_POST['form_'.$split[0].'_card_no']) == "" || !isset($_POST['form_'.$split[0].'_card_holder']) || trim($_POST['form_'.$split[0].'_card_holder']) == "" || !isset($_POST['form_'.$split[0].'_expire_date']) || trim($_POST['form_'.$split[0].'_expire_date']) == "")) {
						echo "<data><error>Credit card informations are required fields.</error></data>";
						exit;
					} elseif($split[3] == 'payment' && $_POST['form_'.$split[0]] != 'bank') {
						$data .= "Card No: ".$_POST['form_'.$split[0].'_card_no']."\n";
						$data .= "Card Holder: ".$_POST['form_'.$split[0].'_card_holder']."\n";
						$data .= "Expire Date: ".$_POST['form_'.$split[0].'_expire_date']."\n";
					}
				}
			}
			// Check for CAPTCHA
			if($captcha) {
				if(session_id() == '') session_start();
				if(!isset($_SESSION['captcha']) || $_SESSION['captcha'] == false) {
					if(!isset($_POST['captcha_code']) || $_POST['captcha_code'] == "") {
						echo "<data><error>No CAPTCHA Code entered.</error></data>";
						exit;
					}
					include_once('securimage/securimage.php');
					$securimage = new Securimage();
					if ($securimage->check($_POST['captcha_code']) == false) {
						echo "<data><error>Wrong CAPTCHA Code.</error></data>";
						exit;
					}
				}
			}
			// Write to db if specified
			if($save_to_db == "1") 
				$utility->query("INSERT INTO `".$utility->prefix()."mod_inlineforms_data` VALUES ('".$name."','".$data."','".date(DATE_RFC822)."');");
			// Send emails if specified
			foreach($emails as $email)
				mail($email, "New ".$name." submit", $name." submit:\n".$data, "From: ".$email);
			
			// Return success message
			echo "<data><success>".$success_text."</success></data>";
			exit;
		} else {
			echo "FAIL";
			exit;
		}
	} elseif(isset($_POST['captcha_code'])) {
		if(session_id() == '') session_start();
		require_once(dirname(__FILE__).'/../../thirdparty/securimage/securimage.php');
		$securimage = new Securimage();

		if ($securimage->check($_POST['captcha_code']) == false) {
			echo "FALSE";
			$_SESSION['captcha'] = false;
		}
		else {
			echo "TRUE";
			$_SESSION['captcha'] = true;
		}

		exit;
	} elseif(isset($_GET['id_string'])) {
		$string = urldecode($_GET['id_string']);
		$string = strip_tags($string);
		$string = preg_replace("/[^A-Za-z0-9\-_]/", '', $string);
		$string = strtolower($string);
		echo $string;
		exit;
	}
?>
