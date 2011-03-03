<?php
/**
 * @package myRepono
 * @version 1.0.5
 */
/*
Plugin Name: myRepono Backup Plugin
Plugin URI: http://myrepono.com/wordpress-backup-plugin/
Description: Automate your WordPress, website &amp; database backups using the <a href="http://myrepono.com/wordpress-backup-plugin/">myRepono remote website backup service</a>.  To get started: 1) Click the 'Activate' link to the left of this description, 2) Go to the 'myRepono Backup' link under the 'Settings' menu on the left of the page.
Author: myRepono (ionix Limited)
Author URI: http://myRepono.com/
License: GPLv2
Version: 1.0.5
*/

function myrepono_get_status() {

	$myrepono_output = "";

	$myrepono_plugin_url = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));

	if ($myrepono_array = get_option("myrepono")) {

		$refresh_cache = "0";
		if (!isset($myrepono_array['myr_cache'])) {
			$refresh_cache = "1";
		} elseif (isset($myrepono_array['myr_cache']['last_refresh'])) {
			if ($myrepono_array['myr_cache']['last_refresh']<(time()-900)) {
				$refresh_cache = "1";
			}
		} else {
			$refresh_cache = "1";
		}

		if (!isset($myrepono_array['myr_username'])) {
			$refresh_cache = "0";
		}

		if ($refresh_cache=="1") {

			$myrepono_hapi_input = array();
			$myrepono_hapi_input['api_key'] = "7GWU-49UY-KJ26-AVG7";
			$myrepono_hapi_input['api_pass'] = "94kao50va5j6s31762se5t";
			$myrepono_hapi['request_url'] = "https://myrepono.com/hapi/h.api?api_key=".$myrepono_hapi_input['api_key']."&api_pass=".$myrepono_hapi_input['api_pass'];
			$myrepono_hapi['request_url_source'] = $myrepono_hapi['request_url']."&api_action=backup-list&email=".rawurlencode($myrepono_array['myr_username'])."&password=".rawurlencode($myrepono_array['myr_password'])."&domain_id=".rawurlencode($myrepono_array['myr_domain_id']);

			$myrepono_hapi['result_source'] = file_get_contents($myrepono_hapi['request_url_source']);
			$myrepono_hapi['result_source'] = explode("|:|\n",$myrepono_hapi['result_source']);

			if (isset($myrepono_hapi['result_source'][0])) {

				$myrepono_hapi['result_source_response'] = explode("|",$myrepono_hapi['result_source'][0]);

				if ($myrepono_hapi['result_source_response'][0]!="1") {

					myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

				} else {

					if ($myrepono_hapi['result_source_response'][2]>0) {

						$myrepono_array['myr_cache']['backup'] = array();

						for ($i=1;$i<count($myrepono_hapi['result_source'])-1;$i++) {

							if (isset($myrepono_hapi['result_source'][$i])) {

								$myrepono_hapi['result_source_response_line'] = explode("|",$myrepono_hapi['result_source'][$i]);

								if ($myrepono_hapi['result_source_response_line'][0]=="BACKUP") {
									$myrepono_array['myr_cache']['backup'][] = array(
										'id' => $myrepono_hapi['result_source_response_line'][1],
										'domain' => $myrepono_hapi['result_source_response_line'][2],
										'server' => $myrepono_hapi['result_source_response_line'][3],
										'filesize_bytes' => $myrepono_hapi['result_source_response_line'][4],
										'filesize' => $myrepono_hapi['result_source_response_line'][5],
										'packets' => $myrepono_hapi['result_source_response_line'][6],
										'archived' => $myrepono_hapi['result_source_response_line'][7],
										'date_string' => $myrepono_hapi['result_source_response_line'][8],
										'date' => $myrepono_hapi['result_source_response_line'][9],
										'time' => $myrepono_hapi['result_source_response_line'][10]
									);

								}
							}
						}
					}

				}

			} else {

				myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

			}

			update_option("myrepono", $myrepono_array);

		}


		if (isset($myrepono_array['myr_cache']['backup'])) {

			$myrepono_backup_keys = array_keys($myrepono_array['myr_cache']['backup']);
			$myrepono_backup_count = count($myrepono_backup_keys);
			for ($i=0; $i<1; $i++) {
				if (isset($myrepono_backup_keys[$i])) {

					$myrepono_backup_key = $myrepono_backup_keys[$i];

					if (isset($myrepono_array['myr_cache']['backup'][$myrepono_backup_key]['id'])) {

						$myrepono_backup_date_string = $myrepono_array['myr_cache']['backup'][$myrepono_backup_key]['date_string'];
						$myrepono_backup_date = $myrepono_array['myr_cache']['backup'][$myrepono_backup_key]['date'];
						$myrepono_backup_time = $myrepono_array['myr_cache']['backup'][$myrepono_backup_key]['time'];

						$myrepono_backup_time_ago = myrepono_time_ago($myrepono_backup_date_string);

						if ($myrepono_backup_time_ago!="") {
							$myrepono_output = "<img src=\"{$myrepono_plugin_url}img/myrepono_icon.png\" width=\"21\" height=\"12\" style=\"position:relative;bottom:-3px;padding-right:2px;\"> <a href=\"options-general.php?page=myrepono\">Last Backup: $myrepono_backup_time_ago ago</a>";

						}
					}
				}
			}

			if ($myrepono_output=="") {

				$myrepono_output = "<img src=\"{$myrepono_plugin_url}img/myrepono_icon.png\" width=\"21\" height=\"12\" style=\"position:relative;bottom:-3px;padding-right:2px;\"> <a href=\"options-general.php?page=myrepono\">Waiting For First Backup</a>";

			}
		}
	}

	if ($myrepono_output=="") {

		$myrepono_output = "<img src=\"{$myrepono_plugin_url}img/myrepono_icon.png\" width=\"21\" height=\"12\" style=\"position:relative;bottom:-3px;padding-right:2px;\"> <a href=\"options-general.php?page=myrepono\">myRepono Plugin Not Configured</a>";
	}

	return $myrepono_output;

}


function myrepono() {

	$myrepono_status = myrepono_get_status();

	print "<p id='myrepono_status'>$myrepono_status</p>";


}


function myrepono_menu() {
	add_options_page('myRepono Backup Settings', 'myRepono Backup', 'manage_options', 'myrepono', 'myrepono_options');
}


function myrepono_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	if (!$myrepono_array = get_option("myrepono")) {

		$myrepono_array = array(
			'myr_username' => "",
			'myr_password' => "",
			'myr_domain_id' => ""
		);
		add_option("myrepono", $myrepono_array, $deprecated, $autoload);

	}

	if ($myrepono_array = get_option("myrepono")) {

		$myrepono_plugin_path = dirname(__FILE__);
		$myrepono_plugin_url = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));

		print <<<END
<div class="wrap">
<div id="icon-options-general" class="icon32"><br></div>
<h2>myRepono WordPress Backup Plugin</h2>


<a href="http://myRepono.com/" target="new"><img src="{$myrepono_plugin_url}img/myrepono_logo_grey_200.gif" width="200" height="55" border="0" align="right" hspace="10" vspace="0" alt="myRepono WordPress Backup Service" title="myRepono WordPress Backup Service" id="myrepono_logo"></a>

<p>myRepono provides a remote WordPress, website and mySQL database backup service.  The service enables you to backup your WordPress data automatically to remote backup servers across the world, for as little as 2 cents per day.
<br>&nbsp;<br>
Useful Links: <a href="http://myRepono.com/" target="new" class="button-secondary"><b>myRepono.com</b></a> <a href="http://myRepono.com/faq/" target="new" class="button-secondary"><b>FAQ &amp; Documentation</b></a> <a href="http://myRepono.com/contact/" target="new" class="button-secondary"><b>Contact Us</b></a>
</p>
END;

		$myrepono_hapi_input = array();
		$myrepono_hapi_input['api_key'] = "7GWU-49UY-KJ26-AVG7";
		$myrepono_hapi_input['api_pass'] = "94kao50va5j6s31762se5t";

		if (!isset($myrepono_array['myr_username'])) {
			$myrepono_array['myr_username'] = "";
		}
		if (!isset($myrepono_array['myr_password'])) {
			$myrepono_array['myr_password'] = "";
		}
		if (!isset($myrepono_array['myr_domain_id'])) {
			$myrepono_array['myr_domain_id'] = "";
		}

		$myrepono_plugin_api_path = $myrepono_plugin_path."/api/";
		$myrepono_plugin_api_url = $myrepono_plugin_url."api/";

		if (!file_exists($myrepono_plugin_api_path)) {
			if (mkdir($myrepono_plugin_api_path,0755,true)) {
			}
		}
		if (!file_exists($myrepono_plugin_api_path."data/")) {
			if (mkdir($myrepono_plugin_api_path."data/",0777,true)) {
			}
		}

		if (!file_exists($myrepono_plugin_api_path)) {
			print<<<END
<div id="message" class="updated"><p><img src="{$myrepono_plugin_url}img/error.png" width="16" height="16" alt="" style="position:relative;top:2px;">&nbsp; <b>Warning:</b> Unable to create myRepono API directory ($myrepono_plugin_api_path).<p></div>
END;
		} elseif (!file_exists($myrepono_plugin_api_path."data/")) {
			print<<<END
<div id="message" class="updated"><p><img src="{$myrepono_plugin_url}img/error.png" width="16" height="16" alt="" style="position:relative;top:2px;">&nbsp; <b>Warning:</b> Unable to create myRepono API data directory ({$myrepono_plugin_api_path}data/).<p></div>
END;
		}

		if (stristr(WP_PLUGIN_URL,"127.0.0.1")) {
			print<<<END
<div id="message" class="updated"><p><img src="{$myrepono_plugin_url}img/error.png" width="16" height="16" alt="" style="position:relative;top:2px;">&nbsp; <b>Warning:</b> Detected URL is 127.0.0.1/localhost, the myRepono system will be unable to connect to your local server to backup your WordPress installation.  The myRepono WordPress Backup Plugin can only be used with WordPress installations which are on public web servers.<p></div>
END;
		}


		$myrepono_response = "";
		$myrepono_login_response = "";

		$myrepono_submit = "";
		if (isset($_POST["submit"])) {
			$myrepono_submit = mysql_escape_string(htmlentities(strip_tags($_POST["submit"])));
		}
		$myrepono_input_email = "";
		if (isset($_POST["myrepono_email"])) {
			$myrepono_input_email = mysql_escape_string(htmlentities(strip_tags($_POST["myrepono_email"])));
		}

		if ($myrepono_submit=="View Domain") {

			$myrepono_input_select_domains = "";
			if (isset($_POST["myrepono_select_domains"])) {
				$myrepono_input_select_domains = mysql_escape_string(htmlentities(strip_tags($_POST["myrepono_select_domains"])));
			}

			if ($myrepono_input_select_domains!="") {
				if (is_numeric($myrepono_input_select_domains)) {

					$myrepono_domains_keys = array_keys($myrepono_array['myr_cache']['domains']);

					for ($i=0; $i<count($myrepono_domains_keys); $i++) {

						$myrepono_domains_key = $myrepono_domains_keys[$i];

						if ($myrepono_array['myr_cache']['domains'][$myrepono_domains_key]['id']==$myrepono_input_select_domains) {

							$myrepono_array['myr_domain_id'] = $myrepono_input_select_domains;
							$myrepono_domain_id = $myrepono_array['myr_domain_id'];

							$myrepono_array['myr_cache']['domain'] = array();

							$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['frequency'] = $myrepono_array['myr_cache']['domains'][$myrepono_domains_key]['frequency'];
							$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['frequency_days'] = $myrepono_array['myr_cache']['domains'][$myrepono_domains_key]['frequency_days'];
							$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['stored'] = $myrepono_array['myr_cache']['domains'][$myrepono_domains_key]['active'];
							$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['active'] = $myrepono_array['myr_cache']['domains'][$myrepono_domains_key]['stored'];
							$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['primary_location'] = $myrepono_array['myr_cache']['domains'][$myrepono_domains_key]['primary_location'];

							$myrepono_array['myr_cache']['backup'] = array();

							$myrepono_array['myr_cache']['last_refresh'] = "";
							$myrepono_array['myr_cache']['last_refresh_delay'] = "";

							update_option("myrepono", $myrepono_array);

							$i = "999999999";

						}

					}
				}
			}

		} elseif ($myrepono_submit=="Confirm Reset Default Configuration") {

			$myrepono_array = array();
			$myrepono_array['myr_username'] = "";
			$myrepono_array['myr_password'] = "";
			$myrepono_array['myr_domain_id'] = "";
			update_option("myrepono", $myrepono_array);

		} elseif ($myrepono_submit=="Log-in to myRepono Account") {

			$myrepono_login_successful = "0";

			$myrepono_input_password = "";
			if (isset($_POST["myrepono_password"])) {
				$myrepono_input_password = mysql_escape_string(htmlentities(strip_tags($_POST["myrepono_password"])));
			}

			$myrepono_input_login_authenticate = "";
			if (isset($_POST["myrepono_login_authenticate"])) {
				$myrepono_input_login_authenticate = mysql_escape_string(htmlentities(strip_tags($_POST["myrepono_login_authenticate"])));
			}

			if (($myrepono_input_email!="") && ($myrepono_input_password!="")) {

				$myrepono_hapi['request_url'] = "https://myrepono.com/hapi/h.api?api_key=".$myrepono_hapi_input['api_key']."&api_pass=".$myrepono_hapi_input['api_pass'];

				$myrepono_hapi['request_url_source'] = $myrepono_hapi['request_url']."&api_action=authenticate&email=".rawurlencode($myrepono_input_email)."&password=".rawurlencode($myrepono_input_password);

				$myrepono_hapi['result_source'] = file_get_contents($myrepono_hapi['request_url_source']);

				$myrepono_hapi['result_source'] = explode("|:|\n",$myrepono_hapi['result_source']);

				if (isset($myrepono_hapi['result_source'][0])) {

					$myrepono_hapi['result_source_response'] = explode("|",$myrepono_hapi['result_source'][0]);

					if ($myrepono_hapi['result_source_response'][0]!="1") {

						myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

					} else {

						$myrepono_array['myr_username'] = $myrepono_input_email;
						$myrepono_array['myr_password'] = $myrepono_input_password;
						$myrepono_array['myr_domain_id'] = "";
						update_option("myrepono", $myrepono_array);

						$myrepono_login_successful = "1";

						if ($myrepono_input_login_authenticate=="1") {

							$myrepono_hapi_input['password'] = $myrepono_array['myr_password'];
							update_option("myrepono", $myrepono_array);

						} else {

							$myrepono_response = "<img src=\"{$myrepono_plugin_url}img/accept.png\" width=\"16\" height=\"16\" alt=\"\" style=\"position:relative;top:2px;\">&nbsp; Login Successful!<br>&nbsp;<br>Your plugin and backup API have now been setup and your first backup will be processed within the next hour (assuming your backup status remains active).<br>&nbsp;<br>If your first backup fails, please <a href=\"https://myRepono.com/contact/\" target=\"new\"><b>contact support</b></a> for assistance adjusting your API configuration to suit your server.";

							$myrepono_hapi_input['domain_url'] = $myrepono_plugin_api_url;
							$myrepono_hapi_input['domain_path'] = $myrepono_plugin_api_path;
							$myrepono_hapi_input['email'] = $myrepono_array['myr_username'];
							$myrepono_hapi_input['password'] = $myrepono_array['myr_password'];

							$myrepono_hapi_input['domain_backup_path'] = dirname(dirname(dirname($myrepono_plugin_path)));
							$myrepono_hapi_input['domain_database_host'] = DB_HOST;
							$myrepono_hapi_input['domain_database_name'] = DB_NAME;
							$myrepono_hapi_input['domain_database_user'] = DB_USER;
							$myrepono_hapi_input['domain_database_pass'] = DB_PASSWORD;

							$myrepono_hapi_input['skip_signup'] = "1";

							$myrepono_hapi_response = myrepono_hapi_auto_setup($myrepono_hapi_input);

							if ($myrepono_hapi_response['result']=="1") {

								$myrepono_array['myr_domain_id'] = $myrepono_hapi_response['domain_id'];
								update_option("myrepono", $myrepono_array);

								/*
								if (file_exists($myrepono_plugin_api_path."myrepono.php")) {
									chmod($myrepono_plugin_api_path,0777);
									if ($myrepono_api_response = file_get_contents($myrepono_plugin_api_url."myrepono.php")) {
										if ($myrepono_api_response!="0") {
											chmod($myrepono_plugin_api_path,0755);
										}
									} else {
										chmod($myrepono_plugin_api_path,0755);
									}
								}
								*/

								if (!file_exists($myrepono_plugin_api_path."data/")) {
									if (mkdir($myrepono_plugin_api_path."data/",0777,true)) {
									}
								}

							}

						}

					}

				} else {

					myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

				}


				if ($myrepono_login_successful!="1") {
					if ($myrepono_hapi['result_source_response'][2]=="User Not Active") {
						$myrepono_hapi['result_source_response'][2] = "Account is inactive, please ensure you have completed the email activation stage.";
					} else {
						$myrepono_hapi['result_source_response'][2] = "Invalid login details.";
					}
					$myrepono_login_response = "Login error: ".$myrepono_hapi['result_source_response'][2];
				}

			} else {

				$myrepono_login_response = "Please enter your myRepono log-in details or create a new account.";

			}


		}

		if (($myrepono_array['myr_username']=="") || ($myrepono_array['myr_password']=="")) {

			$myrepono_input_currency = "";
			if (isset($_POST["myrepono_currency"])) {
				$myrepono_input_currency = mysql_escape_string(htmlentities(strip_tags($_POST["myrepono_currency"])));
			}
			$myrepono_input_terms = "";
			if (isset($_POST["myrepono_terms"])) {
				$myrepono_input_terms = mysql_escape_string(htmlentities(strip_tags($_POST["myrepono_terms"])));
			}

			$myrepono_terms_checked = "";
			if ($myrepono_input_terms=="1") {
				$myrepono_terms_checked = " checked";
			}

			if (($myrepono_input_currency=="USD") || ($myrepono_input_currency=="GBP") || ($myrepono_input_currency=="EUR")) {
				if ($myrepono_input_email=="") {

					$myrepono_response = "<img src=\"{$myrepono_plugin_url}img/error.png\" width=\"16\" height=\"16\" alt=\"\" style=\"position:relative;top:2px;\">&nbsp; Please enter your email address.";

				} elseif (myrepono_validate_email($myrepono_input_email)!="1") {

					$myrepono_response = "<img src=\"{$myrepono_plugin_url}img/error.png\" width=\"16\" height=\"16\" alt=\"\" style=\"position:relative;top:2px;\">&nbsp; Please enter a valid email address.";

				} elseif ($myrepono_input_terms!="1") {

					$myrepono_response = "<img src=\"{$myrepono_plugin_url}img/error.png\" width=\"16\" height=\"16\" alt=\"\" style=\"position:relative;top:2px;\">&nbsp; Please agree to the terms of service.";

				} else {

					$myrepono_hapi_input['domain_url'] = $myrepono_plugin_api_url;
					$myrepono_hapi_input['domain_path'] = $myrepono_plugin_api_path;
					$myrepono_hapi_input['email'] = $myrepono_input_email;
					$myrepono_hapi_input['currency'] = $myrepono_input_currency;

					$myrepono_hapi_input['domain_backup_path'] = dirname(dirname(dirname($myrepono_plugin_path)));
					$myrepono_hapi_input['domain_database_host'] = DB_HOST;
					$myrepono_hapi_input['domain_database_name'] = DB_NAME;
					$myrepono_hapi_input['domain_database_user'] = DB_USER;
					$myrepono_hapi_input['domain_database_pass'] = DB_PASSWORD;

					$myrepono_hapi_response = myrepono_hapi_auto_setup($myrepono_hapi_input);

					//print "<pre>".print_r($myrepono_hapi_response,1)."</pre>";

					if ($myrepono_hapi_response['result']=="1") {

						//$myrepono_array['myr_username'] = $myrepono_hapi_input['email'];
						//$myrepono_array['myr_password'] = $myrepono_hapi_response['password'];
						//$myrepono_array['myr_domain_id'] = $myrepono_hapi_response['domain_id'];
						//update_option("myrepono", $myrepono_array);

						$myrepono_response = "<img src=\"{$myrepono_plugin_url}img/accept.png\" width=\"16\" height=\"16\" alt=\"\" style=\"position:relative;top:2px;\">&nbsp; <b>Sign-Up Completed Successfully!</b><br>Log-In Email: ".$myrepono_hapi_input['email']."<br><b>Please Check Your Email!</b><br>An activation link has been sent via email, please select this link to activate your account and then enter your login password below.";

						/*
						if (file_exists($myrepono_plugin_api_path."myrepono.php")) {
							if ($myrepono_api_response = file_get_contents($myrepono_plugin_api_url."myrepono.php")) {
								if ($myrepono_api_response!="0") {
									chmod($myrepono_plugin_api_path,0755);
								}
							} else {
								chmod($myrepono_plugin_api_path,0755);
							}
						}
						*/

					} else {

						$myrepono_response = "Error creating account: ".$myrepono_hapi_response['error_msg'];

					}
				}
			}
		}

		if (($myrepono_array['myr_username']=="") || ($myrepono_array['myr_password']=="")) {

			$myrepono_free_trial_topup_credit = "";
			$myrepono_free_trial_topup_usd = "";
			$myrepono_free_trial_topup_gbp = "";
			$myrepono_free_trial_topup_eur = "";
			if ($myrepono_free_trial_topup_credit = file_get_contents("http://myRepono.com/sys/free_trial_credit/")) {

				$myrepono_free_trial_topup_credit = explode("|",$myrepono_free_trial_topup_credit);
				if ((!is_numeric($myrepono_free_trial_topup_credit[1])) || (!is_numeric($myrepono_free_trial_topup_credit[2])) || (!is_numeric($myrepono_free_trial_topup_credit[3]))) {
					$myrepono_free_trial_topup_credit = "";
				} else {
					$myrepono_free_trial_topup_usd = $myrepono_free_trial_topup_credit[1];
					$myrepono_free_trial_topup_gbp = $myrepono_free_trial_topup_credit[2];
					$myrepono_free_trial_topup_eur = $myrepono_free_trial_topup_credit[3];
				}

			} else {

				print<<<END
<div class="myrepono_response"><p><img src="{$myrepono_plugin_url}img/error.png" width="16" height="16" alt="" style="position:relative;top:2px;">&nbsp; <b>Warning:</b> Initial tests demonstate this plugin is unable to connect to myRepono.com, therefore the create account and log-in processes may not work correctly.<p></div>
END;

			}

			if ($myrepono_free_trial_topup_usd>0) {
				$myrepono_free_trial_topup_usd = " - \$$myrepono_free_trial_topup_usd Free Credit";
			} else {
				$myrepono_free_trial_topup_usd = "";
			}
			if ($myrepono_free_trial_topup_gbp>0) {
				$myrepono_free_trial_topup_gbp = " - &pound;$myrepono_free_trial_topup_gbp Free Credit";
			} else {
				$myrepono_free_trial_topup_gbp = "";
			}
			if ($myrepono_free_trial_topup_eur>0) {
				$myrepono_free_trial_topup_eur = " - &euro;$myrepono_free_trial_topup_eur Free Credit";
			} else {
				$myrepono_free_trial_topup_eur = "";
			}

			$myrepono_currency_selected_usd = "";
			$myrepono_currency_selected_gbp = "";
			$myrepono_currency_selected_eur = "";
			if ($myrepono_input_currency=="USD") {
				$myrepono_currency_selected_usd = " selected";
			} elseif ($myrepono_input_currency=="GBP") {
				$myrepono_currency_selected_gbp = " selected";
			} elseif ($myrepono_input_currency=="EUR") {
				$myrepono_currency_selected_eur = " selected";
			}

			$myrepono_currency_select = <<<END
<option value="USD"$myrepono_currency_selected_usd>USD (\$) - United States Dollars$myrepono_free_trial_topup_usd</option>
<option value="GBP"$myrepono_currency_selected_gbp>GBP (&pound;) - British Pounds Sterling$myrepono_free_trial_topup_gbp</option>
<option value="EUR"$myrepono_currency_selected_eur>EUR (&euro;) - Euros$myrepono_free_trial_topup_eur</option>
END;

			if ($myrepono_response!="") {

				$myrepono_response = <<<END
<div class="myrepono_response"><p>$myrepono_response<p></div>
END;

			}


			if ($myrepono_login_response!="") {

				$myrepono_login_response = <<<END
<div class="myrepono_response"><p>$myrepono_login_response<p></div>
END;

			}

			print<<<END

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td valign="top" width="70%">

<form name="form" action="options-general.php?page=myrepono" method="post">

<h3>Create myRepono Account</h3>
<p>To begin, please enter your email address to sign-up for a myRepono.com account.  Registration is free, no payment details are required until you choose to top-up your balance. All information is stored with the strictest confidence, for more information please see our <A href="http://myrepono.com/privacy/" target="new"><b>Privacy Policy</b></a>.</p>

$myrepono_response

<table class="form-table">
	<tbody>
	<tr>
		<th>
			<label><b>Email Address:</b></label>
		</th>
		<td>
			<input name="myrepono_email" id="myrepono_email" value="$myrepono_input_email" class="regular-text code" type="text">
		</td>
	</tr>
	<tr>
		<th>
			<label><b>Account Currency:</b></label>
		</th>
		<td>
			<select name="myrepono_currency" id="myrepono_currency" class="regular-text code">
			$myrepono_currency_select
			</select>
		</td>
	</tr>
	<tr>
		<th>
		</th>
		<td>
			<label>
			<input name="myrepono_terms" id="myrepono_terms" value="1" type="checkbox"$myrepono_terms_checked>
			 I have read, understand and agree to the <A href="http://myrepono.com/terms/" target="new"><b>Terms of Service</b></a>.</label>
		</td>
	</tr></tbody></table>

<p class="submit">
	<input name="submit" class="button-primary" value="Create myRepono Account" type="submit">
</p>

</form>

<br>

<form name="form" action="options-general.php?page=myrepono" method="post">

<h3>Log-in to myRepono Account</h3>
<p>If you already have a myRepono.com account, please log-in using the form below.  Note, a new domain configuration will be created in your myRepono account for the WordPress plugin.</p>

$myrepono_login_response

<table class="form-table">
	<tbody>
	<tr>
		<th>
			<label><b>Email Address:</b></label>
		</th>
		<td>
			<input name="myrepono_email" id="myrepono_email" value="$myrepono_input_email" class="regular-text code" type="text">
		</td>
	</tr>
	<tr>
		<th>
			<label><b>Password:</b></label>
		</th>
		<td>
			<input name="myrepono_password" id="myrepono_password" value="" type="password" class="regular-text code"> <a href="http://myRepono.com/password/" target="new"><b>Lost your password?</b></a>
		</td>
	</tr></tbody></table>

<p class="submit">
	<input name="submit" class="button-primary" value="Log-in to myRepono Account" type="submit">
</p>

</form>

</td><td width="30" style="width:30px;">&nbsp;</td><td width="29%" valign="top">


<table class="widefat">
<thead>
    <tr>
        <th><b>What is myRepono?</b></th>
    </tr>
<tbody>
   <tr>
     <td>

	<p>myRepono is a remote website backup service which enables you to backup your WordPress files and databases.</p>

	<p>myRepono is a commercial backup service which uses a pay-as-you-go balance system.  Users receive \$5 USD free credit to help them get started, and with prices starting at 2 cents per day that's enough free credit to backup most WordPress installations for several months!</p>

	<p>We welcome your comments and feedback, and we're always available to help and assist you, if you have any feedback or questions please don't hesitate to <a href="https://myRepono.com/contact/" target="new"><b>contact us</b></a>!</p>


     </td>
   </tr>
</tbody>
</table>
<br>


</td></tr>
</table>


END;

		} else {

			$myrepono_login_reauthenticate = "0";

			$myrepono_username = $myrepono_array['myr_username'];
			$myrepono_domain_id = $myrepono_array['myr_domain_id'];

			$refresh_cache = "0";
			if (!isset($myrepono_array['myr_cache'])) {
				$refresh_cache = "1";
			} elseif (isset($myrepono_array['myr_cache']['last_refresh'])) {
				if ($myrepono_array['myr_cache']['last_refresh']<(time()-60)) {
					$refresh_cache = "1";
				}
			} else {
				$refresh_cache = "1";
			}

			if ($myrepono_submit=="Save Changes") {

				$myrepono_input_frequency = "";
				if (isset($_POST["myrepono_frequency"])) {
					$myrepono_input_frequency = mysql_escape_string(htmlentities(strip_tags($_POST["myrepono_frequency"])));
				}

				$myrepono_input_frequency_days = "";
				if (isset($_POST["myrepono_frequency_days"])) {
					$myrepono_input_frequency_days = mysql_escape_string(htmlentities(strip_tags($_POST["myrepono_frequency_days"])));
				}

				$myrepono_input_stored = "";
				if (isset($_POST["myrepono_stored"])) {
					$myrepono_input_stored = mysql_escape_string(htmlentities(strip_tags($_POST["myrepono_stored"])));
				}

				$myrepono_input_primary_location = "";
				if (isset($_POST["myrepono_primary_location"])) {
					$myrepono_input_primary_location = mysql_escape_string(htmlentities(strip_tags($_POST["myrepono_primary_location"])));
				}

				$myrepono_input_mirror_location = "";
				if (isset($_POST["myrepono_mirror_location"])) {
					if (is_array($_POST["myrepono_mirror_location"])) {
						$myrepono_input_mirror_location = mysql_escape_string(htmlentities(strip_tags(implode(",",$_POST["myrepono_mirror_location"]))));
					} else {
						$myrepono_input_mirror_location = mysql_escape_string(htmlentities(strip_tags($_POST["myrepono_mirror_location"])));
					}
				}

				$myrepono_input_active = "";
				if (isset($_POST["myrepono_active"])) {
					$myrepono_input_active = mysql_escape_string(htmlentities(strip_tags($_POST["myrepono_active"])));
				}


				$myrepono_settings_saved = "0";

				$myrepono_hapi['request_url'] = "https://myrepono.com/hapi/h.api?api_key=".$myrepono_hapi_input['api_key']."&api_pass=".$myrepono_hapi_input['api_pass'];

				$myrepono_hapi['request_url_source'] = $myrepono_hapi['request_url']."&api_action=domain-settings&email=".rawurlencode($myrepono_array['myr_username'])."&password=".rawurlencode($myrepono_array['myr_password'])."&domain_id=".rawurlencode($myrepono_domain_id)."&set_frequency=".rawurlencode($myrepono_input_frequency)."&set_frequency_days=".rawurlencode($myrepono_input_frequency_days)."&set_stored=".rawurlencode($myrepono_input_stored)."&set_active=".rawurlencode($myrepono_input_active)."&set_primary_location=".rawurlencode($myrepono_input_primary_location);

				$myrepono_hapi['result_source'] = file_get_contents($myrepono_hapi['request_url_source']);
				$myrepono_hapi['result_source'] = explode("|:|\n",$myrepono_hapi['result_source']);

				if (isset($myrepono_hapi['result_source'][0])) {

					$myrepono_hapi['result_source_response'] = explode("|",$myrepono_hapi['result_source'][0]);

					if ($myrepono_hapi['result_source_response'][0]!="1") {

						myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

					} else {

						$myrepono_settings_saved = "1";

						$myrepono_array['myr_cache']['domain'] = array();
						$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['frequency'] = $myrepono_input_frequency;
						$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['frequency_days'] = $myrepono_input_frequency_days;
						$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['stored'] = $myrepono_input_stored;
						$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['active'] = $myrepono_input_active;
						$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['primary_location'] = $myrepono_input_primary_location;
						$myrepono_array['myr_cache']['last_refresh'] = time();

						update_option("myrepono", $myrepono_array);

					}

				} else {

					myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

				}

				if ($myrepono_settings_saved=="1") {

					$myrepono_response = "<img src=\"{$myrepono_plugin_url}img/accept.png\" width=\"16\" height=\"16\" alt=\"\" style=\"position:relative;top:2px;\">&nbsp; Backup configuration successfully updated.";

				}
			}

			$unknown_user_balance = "0";

			if ($refresh_cache=="1") {

				$unknown_user_balance = "1";

				$myrepono_hapi['request_url'] = "https://myrepono.com/hapi/h.api?api_key=".$myrepono_hapi_input['api_key']."&api_pass=".$myrepono_hapi_input['api_pass'];

				$myrepono_hapi['request_url_source'] = $myrepono_hapi['request_url']."&api_action=balance&email=".rawurlencode($myrepono_array['myr_username'])."&password=".rawurlencode($myrepono_array['myr_password']);

				$myrepono_hapi['result_source'] = file_get_contents($myrepono_hapi['request_url_source']);
				$myrepono_hapi['result_source'] = explode("|:|\n",$myrepono_hapi['result_source']);

				if (isset($myrepono_hapi['result_source'][0])) {

					$myrepono_hapi['result_source_response'] = explode("|",$myrepono_hapi['result_source'][0]);

					if ($myrepono_hapi['result_source_response'][0]!="1") {

						if (($myrepono_hapi['result_source_response'][1]=="2") || ($myrepono_hapi['result_source_response'][1]=="6") || ($myrepono_hapi['result_source_response'][1]=="7") || ($myrepono_hapi['result_source_response'][1]=="8")) {

							$myrepono_login_reauthenticate = "1";

							print<<<END
<table width="100%" cellpadding=0 cellspacing=0 border=0>
<tr><td width="62%" valign="top">

<div class="myrepono_response"><p><img src="{$myrepono_plugin_url}img/error.png" width="16" height="16" alt="" style="position:relative;top:2px;">&nbsp; <b>Warning:</b> Invalid myRepono account details set, if you have changed your password please enter your new password below.<br>

<form name="form" action="options-general.php?page=myrepono" method="post">
<input name="myrepono_login_authenticate" id="myrepono_login_authenticate" value="1" type="hidden">

$myrepono_login_response

<table class="form-table">
	<tbody>
	<tr>
		<th>
			<b>Re-Authenticate Log-In</b>
		</th>
		<td>
		</td>
	</tr>
	<tr>
		<th>
			<label><b>Email Address:</b></label>
		</th>
		<td>
			$myrepono_username
			<input name="myrepono_email" id="myrepono_email" value="$myrepono_username" type="hidden">
		</td>
	</tr>
	<tr>
		<th>
			<label><b>Password:</b></label>
		</th>
		<td>
			<input name="myrepono_password" id="myrepono_password" value="" type="password" class="regular-text code"><br><a href="http://myRepono.com/password/" target="new"><b>Lost your password?</b></a>
		</td>
	</tr></tbody></table>

<p class="submit">
	<input name="submit" class="button-primary" value="Log-in to myRepono Account" type="submit">
</p>

</form>

<p>To log-in with a different username, select the 'Reset Default Configuration' button on the right and re-login.<p></div>

</td><td width="30" style="width:30px;">&nbsp;</td><td width="35%" valign="top">

<table class="widefat">
<thead>
    <tr>
        <th><b>Change myRepono Account</b></th>
    </tr>
<tbody>
   <tr>
     <td>

	<p>Your myRepono plugin is currently configured for your myRepono.com account, <b>$myrepono_username</b>.  Please select the button below to clear your current configuration, this will not disrupt your backups or domain configuration which will remain stored and active in your account on myRepono.com.</p>
	<form name="form" action="options-general.php?page=myrepono" method="post">
	<p class="submit" style="margin-top:6px;padding-top:0px;margin-bottom:6px;padding-bottom:0px;">
		<input name="button" class="button-primary" value="Reset Default Configuration" type="button" onclick="this.style.display='none';document.getElementById('myrepono_reset_config').style.display='block';">
		<input name="submit" class="button-primary" value="Confirm Reset Default Configuration" type="submit" id="myrepono_reset_config" style="display:none;">
	</p>
	</form>


     </td>
   </tr>
</tbody>
</table>
<br>

</td></tr>
</table>
END;

						}

						myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

					} else {

						$myrepono_balance = explode(":", $myrepono_hapi['result_source_response'][2]);

						$myrepono_array['myr_cache']['user_balance'] = $myrepono_balance[0];
						$myrepono_array['myr_cache']['user_currency'] = $myrepono_balance[1];

						$unknown_user_balance = "0";

					}

				} else {

					myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

				}


				$myrepono_hapi['request_url_source'] = $myrepono_hapi['request_url']."&api_action=backup-list&email=".rawurlencode($myrepono_array['myr_username'])."&password=".rawurlencode($myrepono_array['myr_password'])."&domain_id=".rawurlencode($myrepono_array['myr_domain_id']);

				$myrepono_hapi['result_source'] = file_get_contents($myrepono_hapi['request_url_source']);
				$myrepono_hapi['result_source'] = explode("|:|\n",$myrepono_hapi['result_source']);

				if (isset($myrepono_hapi['result_source'][0])) {

					$myrepono_hapi['result_source_response'] = explode("|",$myrepono_hapi['result_source'][0]);

					if ($myrepono_hapi['result_source_response'][0]!="1") {

						myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

					} else {

						$myrepono_array['myr_cache']['backup'] = array();

						if ($myrepono_hapi['result_source_response'][2]>0) {

							for ($i=1;$i<count($myrepono_hapi['result_source'])-1;$i++) {

								if (isset($myrepono_hapi['result_source'][$i])) {

									$myrepono_hapi['result_source_response_line'] = explode("|",$myrepono_hapi['result_source'][$i]);

									if ($myrepono_hapi['result_source_response_line'][0]=="BACKUP") {
										$myrepono_array['myr_cache']['backup'][] = array(
											'id' => $myrepono_hapi['result_source_response_line'][1],
											'domain' => $myrepono_hapi['result_source_response_line'][2],
											'server' => $myrepono_hapi['result_source_response_line'][3],
											'filesize_bytes' => $myrepono_hapi['result_source_response_line'][4],
											'filesize' => $myrepono_hapi['result_source_response_line'][5],
											'packets' => $myrepono_hapi['result_source_response_line'][6],
											'archived' => $myrepono_hapi['result_source_response_line'][7],
											'date_string' => $myrepono_hapi['result_source_response_line'][8],
											'date' => $myrepono_hapi['result_source_response_line'][9],
											'time' => $myrepono_hapi['result_source_response_line'][10]
										);

									}
								}
							}
						}

					}

				} else {

					myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

				}

				if (!isset($myrepono_array['myr_cache']['last_refresh_delay'])) {
					$myrepono_array['myr_cache']['last_refresh_delay'] = "0";
				}

				if ($myrepono_array['myr_cache']['last_refresh_delay']<(time()-300)) {

					$myrepono_hapi['request_url_source'] = $myrepono_hapi['request_url']."&api_action=locations&email=".rawurlencode($myrepono_array['myr_username'])."&password=".rawurlencode($myrepono_array['myr_password']);

					$myrepono_hapi['result_source'] = file_get_contents($myrepono_hapi['request_url_source']);
					$myrepono_hapi['result_source'] = explode("|:|\n",$myrepono_hapi['result_source']);

					if (isset($myrepono_hapi['result_source'][0])) {

						$myrepono_hapi['result_source_response'] = explode("|",$myrepono_hapi['result_source'][0]);

						if ($myrepono_hapi['result_source_response'][0]!="1") {

							myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

						} else {

							$myrepono_locations_explode = explode(":", $myrepono_hapi['result_source_response'][2]);
							$myrepono_locations = array();

							for ($i=0; $i<count($myrepono_locations_explode); $i++) {
								$myrepono_locations_tmp = explode("=",$myrepono_locations_explode[$i]);
								$myrepono_locations_tmp_id = $myrepono_locations_tmp[0];
								$myrepono_locations[$myrepono_locations_tmp_id]['flag'] = $myrepono_locations_tmp[1];
								$myrepono_locations[$myrepono_locations_tmp_id]['name'] = $myrepono_locations_tmp[2];
							}

							$myrepono_array['myr_cache']['locations'] = $myrepono_locations;
							$myrepono_array['myr_cache']['last_refresh'] = time();

						}

					} else {

						myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

					}


					$myrepono_hapi['request_url_source'] = $myrepono_hapi['request_url']."&api_action=domain-list&email=".rawurlencode($myrepono_array['myr_username'])."&password=".rawurlencode($myrepono_array['myr_password']);

					$myrepono_hapi['result_source'] = file_get_contents($myrepono_hapi['request_url_source']);
					$myrepono_hapi['result_source'] = explode("|:|\n",$myrepono_hapi['result_source']);

					if (isset($myrepono_hapi['result_source'][0])) {

						$myrepono_hapi['result_source_response'] = explode("|",$myrepono_hapi['result_source'][0]);

						if ($myrepono_hapi['result_source_response'][0]!="1") {

							myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

						} else {

							$myrepono_array['myr_cache']['domains'] = array();
							$myrepono_array['myr_cache']['domain'] = array();

							if ($myrepono_hapi['result_source_response'][2]>0) {

								for ($i=1;$i<count($myrepono_hapi['result_source'])-1;$i++) {

									if (isset($myrepono_hapi['result_source'][$i])) {

										$myrepono_hapi['result_source_response_line'] = explode("|",$myrepono_hapi['result_source'][$i]);

										if ($myrepono_hapi['result_source_response_line'][0]=="DOMAIN") {

											$myrepono_domains_domain_id =  $myrepono_hapi['result_source_response_line'][1];

											$myrepono_array['myr_cache']['domains'][] = array(
												'id' => $myrepono_hapi['result_source_response_line'][1],
												'api_url' => $myrepono_hapi['result_source_response_line'][2],
												'api_key' => $myrepono_hapi['result_source_response_line'][3],
												'api_name' => $myrepono_hapi['result_source_response_line'][4],
												'frequency' => $myrepono_hapi['result_source_response_line'][5],
												'frequency_days' => $myrepono_hapi['result_source_response_line'][6],
												'stored' => $myrepono_hapi['result_source_response_line'][7],
												'active' => $myrepono_hapi['result_source_response_line'][8],
												'primary_location' => $myrepono_hapi['result_source_response_line'][9]
											);

											if ($myrepono_hapi['result_source_response_line'][1]==$myrepono_array['myr_domain_id']) {

												$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['frequency'] = $myrepono_hapi['result_source_response_line'][5];
												$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['frequency_days'] = $myrepono_hapi['result_source_response_line'][6];
												$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['stored'] = $myrepono_hapi['result_source_response_line'][7];
												$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['active'] = $myrepono_hapi['result_source_response_line'][8];
												$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['primary_location'] = $myrepono_hapi['result_source_response_line'][9];

											}

											$myrepono_array['myr_cache']['last_refresh'] = time();

										}
									}
								}
							}

						}

					} else {

						myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

					}

					if (count($myrepono_array['myr_cache']['domain'])=="0") {

						$myrepono_hapi['request_url_source'] = $myrepono_hapi['request_url']."&api_action=domain-view&email=".rawurlencode($myrepono_array['myr_username'])."&password=".rawurlencode($myrepono_array['myr_password'])."&domain_id=".rawurlencode($myrepono_array['myr_domain_id']);

						$myrepono_hapi['result_source'] = file_get_contents($myrepono_hapi['request_url_source']);
						$myrepono_hapi['result_source'] = explode("|:|\n",$myrepono_hapi['result_source']);

						if (isset($myrepono_hapi['result_source'][0])) {

							$myrepono_hapi['result_source_response'] = explode("|",$myrepono_hapi['result_source'][0]);

							if ($myrepono_hapi['result_source_response'][0]!="1") {

								myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

							} else {

								$myrepono_domain_explode = explode(":", $myrepono_hapi['result_source_response'][2]);
								$myrepono_array['myr_cache']['domain'] = array();
								$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['frequency'] = $myrepono_domain_explode[0];
								$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['frequency_days'] = $myrepono_domain_explode[1];
								$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['stored'] = $myrepono_domain_explode[2];
								$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['active'] = $myrepono_domain_explode[3];
								$myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['primary_location'] = $myrepono_domain_explode[4];
								$myrepono_array['myr_cache']['last_refresh'] = time();

							}

						} else {

							myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

						}


						$myrepono_array['myr_cache']['last_refresh_delay'] = time();

					}

				}

				update_option("myrepono", $myrepono_array);

			}


			if ($myrepono_response!="") {

				$myrepono_response = <<<END
<div class="myrepono_response"><p>$myrepono_response<p></div>
END;

			}

			// print "<pre>".print_r($myrepono_array,1)."</pre>";

			$backup_stored = "";
			if (isset($myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['stored'])) {
				$backup_stored = $myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['stored'];
			}

			$myrepono_select_stored = "";
			for ($i=1;$i<51;$i++) {
				$myrepono_select_stored .= "<option value=\"$i\"";
				if ($i==$backup_stored) {
					$myrepono_select_stored .= " selected";
				}
				$myrepono_select_stored .= ">$i&nbsp; </option>\n";
			}

			$display_status0_checked = "";
			$display_status1_checked = "";

			$backup_status = "";
			if (isset($myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['active'])) {
				$backup_status = $myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['active'];
			}

			if ($backup_status=="1") {
				$display_status1_checked = " selected";
			} else {
				$display_status0_checked = " selected";
			}

			$backup_frequency = "";
			if (isset($myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['frequency'])) {
				$backup_frequency = $myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['frequency'];
			}

			$backup_frequency_days = "14";
			if (isset($myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['frequency_days'])) {
				$backup_frequency_days = $myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['frequency_days'];
			}

			$display_freq1_checked = "";
			$display_freq2_checked = "";
			$display_freq3_checked = "";
			$display_freq4_checked = "";
			$display_freq5_checked = "";
			$display_freq6_checked = "";
			$myrepono_select_frequency_display = "none";

			if ($backup_frequency=="1") {
				$display_freq1_checked = " selected";
				$display_frequency = "Hourly";
			} elseif ($backup_frequency=="2") {
				$display_freq2_checked = " selected";
				$display_frequency = "Twice Daily";
			} elseif ($backup_frequency=="3") {
				$display_freq3_checked = " selected";
				$display_frequency = "Daily";
			} elseif ($backup_frequency=="4") {
				$display_freq4_checked = " selected";
				$display_frequency = "Weekly";
			} elseif ($backup_frequency=="5") {
				$display_freq5_checked = " selected";
				$display_frequency = "Monthly";
			} elseif ($backup_frequency=="6") {
				$display_freq6_checked = " selected";
				$display_frequency = "Every $backup_frequency_days Days";
				$myrepono_select_frequency_display = "block";
			}

			$myrepono_select_frequency = <<<END

<select name="myrepono_frequency" id="myrepono_frequency" class="regular-text code" onchange="myreponoFrequency(this.value);">
<option value="1"$display_freq1_checked>Hourly</option>
<option value="2"$display_freq2_checked>Twice Daily</option>
<option value="3"$display_freq3_checked>Daily</option>
<option value="4"$display_freq4_checked>Weekly</option>
<option value="5"$display_freq5_checked>Monthly</option>
<option value="6"$display_freq6_checked>Every X Days&nbsp; </option>
</select>
<div id="myrepono_select_frequency_days_div" style="display:$myrepono_select_frequency_display;">
Every <select name="myrepono_frequency_days" id="myrepono_frequency_days" class="regular-text code">
END;

			for ($i=1;$i<31;$i++) {
				$myrepono_select_frequency .= "<option value=\"$i\"";
				if ($backup_frequency_days==$i) {
					$myrepono_select_frequency .= " selected";
				}
				$myrepono_select_frequency .= ">$i &nbsp;</option>";
			}

			$myrepono_select_frequency .= "</select> Days</div>";

			$backup_primary_location = "";
			if (isset($myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['primary_location'])) {
				$backup_primary_location = $myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['primary_location'];
			}

			$backup_mirror_location = "";
			if (isset($myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['mirror_location'])) {
				$backup_mirror_location = $myrepono_array['myr_cache']['domain'][$myrepono_domain_id]['mirror_location'];
			}

			$myrepono_select_primary_location = "";
			$myrepono_select_mirror_location = "";
			$myrepono_select_previous_location = "";

			$location_keys = array_keys($myrepono_array['myr_cache']['locations']);
			$location_count = count($location_keys);

			$backup_mirror_location_explode = array();
			if ($backup_mirror_location!="") {
				$backup_mirror_location_explode = explode(",",$backup_mirror_location);
			}

			for ($i=0;$i<$location_count;$i++) {
				$location_key = $location_keys[$i];
				$myrepono_select_primary_location .= "<div id=\"myrepono_primary_location_$location_key\"><input type=\"radio\" name=\"myrepono_primary_location\" id=\"myrepono_primary_location\" value=\"$location_key\"";
				if ($backup_primary_location==$location_key) {
					$myrepono_select_primary_location .= " checked";
				}
				$myrepono_select_primary_location .= " onclick=\"myreponoPrimaryLocation(this.value);\">&nbsp;".$myrepono_array['myr_cache']['locations'][$location_key]['name']."</div>";

				if ($location_count>1) {

					$myrepono_select_mirror_location_display = "block";
					if ($backup_primary_location==$location_key) {
						$myrepono_select_mirror_location_display = "none";
						$myrepono_select_previous_location = $location_key;
					}
					$myrepono_select_mirror_location .= "<div id=\"myrepono_mirror_location_$location_key\" style=\"display:$myrepono_select_mirror_location_display;\"><input type=\"checkbox\" name=\"myrepono_mirror_location\" id=\"myrepono_mirror_location\" value=\"$location_key\"";
					for ($j=0;$j<count($backup_mirror_location_explode);$j++) {
						if ($backup_mirror_location_explode[$j]==$location_key) {
							$myrepono_select_mirror_location .= " checked";
						}
					}
					$myrepono_select_mirror_location .= ">&nbsp;".$myrepono_array['myr_cache']['locations'][$location_key]['name']."</div>";


				}

			}

			if ($myrepono_select_mirror_location!="") {

				$myrepono_select_mirror_location = <<<END
			<tr>
				<th>
					<label><b>Mirror Location:</b></label>
				</th>
				<td>
				$myrepono_select_mirror_location
				</td>
			</tr>
END;

			}

			$myrepono_backup_count = "0";
			$myrepono_backups = "";
			if (isset($myrepono_array['myr_cache']['backup'])) {
				$myrepono_backup_keys = array_keys($myrepono_array['myr_cache']['backup']);
				$myrepono_backup_count = count($myrepono_backup_keys);
				for ($i=0; $i<$myrepono_backup_count; $i++) {
					$myrepono_backup_key = $myrepono_backup_keys[$i];

					if (isset($myrepono_array['myr_cache']['backup'][$myrepono_backup_key]['id'])) {

						$myrepono_backup_id = $myrepono_array['myr_cache']['backup'][$myrepono_backup_key]['id'];
						$myrepono_backup_filesize = $myrepono_array['myr_cache']['backup'][$myrepono_backup_key]['filesize'];
						$myrepono_backup_server = $myrepono_array['myr_cache']['backup'][$myrepono_backup_key]['server'];
						$myrepono_backup_packets = $myrepono_array['myr_cache']['backup'][$myrepono_backup_key]['packets'];
						$myrepono_backup_archived = $myrepono_array['myr_cache']['backup'][$myrepono_backup_key]['archived'];
						$myrepono_backup_date_string = $myrepono_array['myr_cache']['backup'][$myrepono_backup_key]['date_string'];
						$myrepono_backup_date = $myrepono_array['myr_cache']['backup'][$myrepono_backup_key]['date'];
						$myrepono_backup_time = $myrepono_array['myr_cache']['backup'][$myrepono_backup_key]['time'];

						$myrepono_backup_icon = "server_compressed.png";
						if ($myrepono_backup_archived=="1") {
							$myrepono_backup_icon = "server_save.png";
						}

						$myrepono_backup_location = "USA";
						$myrepono_backup_location_img = "us.png";
						for ($j=0;$j<$location_count;$j++) {
							$location_key = $location_keys[$j];

							if ($myrepono_backup_server==$location_key) {
								$myrepono_backup_location = $myrepono_array['myr_cache']['locations'][$location_key]['name'];
								$myrepono_backup_location_img = $myrepono_array['myr_cache']['locations'][$location_key]['flag'].".png";

							}
						}

						$myrepono_backups .= <<<END
   <tr>
     <td><img src="{$myrepono_plugin_url}img/$myrepono_backup_icon" width="16" height="16"></td>
     <td nowrap>$myrepono_backup_time on $myrepono_backup_date</td>
     <td nowrap><img src="{$myrepono_plugin_url}img/$myrepono_backup_location_img" width="16" height="11" alt="" title="Backups stored in $myrepono_backup_location"> $myrepono_backup_location</td>
     <td nowrap>$myrepono_backup_filesize</td>
     <td nowrap><a href="http://myRepono.com/my/backups/view/$myrepono_backup_id/" class="button-primary" target="new">View</a> <a href="http://myRepono.com/my/backups/restore/$myrepono_backup_id/" class="button-primary" target="new">Restore</a></td>
   </tr>

END;

					}
				}
			}

   			if ($myrepono_backups=="") {

   				$myrepono_backups = <<<END
   <tr>
     <td colspan="5" align="center">&nbsp;<br><b>No backups currently stored.</b><br>&nbsp;</td>
   </tr>
END;

			}

			$myrepono_user_balance = sprintf ("%01.2f", $myrepono_array['myr_cache']['user_balance']);
			if ($myrepono_array['myr_cache']['user_currency']=="GBP") {
				$myrepono_user_balance = "&pound;".$myrepono_user_balance." GBP";
			} elseif ($myrepono_array['myr_cache']['user_currency']=="EUR") {
				$myrepono_user_balance = "&euro;".$myrepono_user_balance." EUR";
			} else {
				$myrepono_user_balance = "\$".$myrepono_user_balance." USD";
			}

			if ($unknown_user_balance=="1") {
				$myrepono_user_balance = "Unavailable";
			}


			if ($myrepono_login_reauthenticate=="0") {

				print<<<END


<table width="100%" cellpadding=0 cellspacing=0 border=0>
<tr><td width="62%" valign="top">

$myrepono_response

END;


				if ($myrepono_array['myr_cache']['user_balance']<2.5) {
					print<<<END
<div class="myrepono_response"><p><img src="{$myrepono_plugin_url}img/error.png" width="16" height="16" alt="" style="position:relative;top:2px;">&nbsp; Your myRepono account balance is <b>$myrepono_user_balance</b>.  We recommend you <a href="https://myRepono.com/my/billing/topup/" target="new"><b>top-up your account balance</b></a> as soon as possible to avoid disruption to your backups.<p></div>
END;

				}

				$myrepono_backups_footer = "";

				if ($myrepono_backup_count>9) {

					$myrepono_backups_footer_disabled = <<<END
<tfoot>
    <tr>
        <th width="16"></th>
        <th>Timestamp</th>
        <th>Location</th>
        <th>Size</th>
        <th width="120">Actions</th>
    </tr>
</tfoot>
END;
				}


				$myrepono_domains_name_title = "";
				$myrepono_domains_name_count = "0";

				if (count($myrepono_array['myr_cache']['domains'])>1) {

					$myrepono_select_domains = "";

					$myrepono_domains_keys = array_keys($myrepono_array['myr_cache']['domains']);

					for ($i=0; $i<count($myrepono_domains_keys); $i++) {

						$myrepono_domains_key = $myrepono_domains_keys[$i];

						if ((isset($myrepono_array['myr_cache']['domains'][$myrepono_domains_key]['id'])) && (isset($myrepono_array['myr_cache']['domains'][$myrepono_domains_key]['api_url']))) {

							if (($myrepono_array['myr_cache']['domains'][$myrepono_domains_key]['id']!="") && ($myrepono_array['myr_cache']['domains'][$myrepono_domains_key]['api_url']!="")) {

								$myrepono_domains_name = "";
								if (isset($myrepono_array['myr_cache']['domains'][$myrepono_domains_key]['api_name'])) {
									if ($myrepono_array['myr_cache']['domains'][$myrepono_domains_key]['api_name']!="") {
										$myrepono_domains_name = $myrepono_array['myr_cache']['domains'][$myrepono_domains_key]['api_name'];
									}
								}
								if ($myrepono_domains_name=="") {
									$myrepono_domains_name = $myrepono_array['myr_cache']['domains'][$myrepono_domains_key]['api_url'];
									$myrepono_domains_name_clean = str_replace("http://","",$myrepono_domains_name);
									$myrepono_domains_name_clean = str_replace("https://","",$myrepono_domains_name_clean);
									$myrepono_domains_name_clean = explode("/",$myrepono_domains_name_clean);
									$myrepono_domains_name = str_replace("www.","",$myrepono_domains_name_clean[0]);
								}

								$myrepono_domains_name_short = substr($myrepono_domains_name,0,35);
								if (substr($myrepono_domains_name,35,1)!="") {
									if (substr($myrepono_domains_name,36,1)!="") {
										$myrepono_domains_name_short .= "...";
									} else {
										$myrepono_domains_name_short = substr($myrepono_domains_name,0,36);
									}
								}

								$myrepono_domains_name = $myrepono_domains_name_short;

								$myrepono_select_domains .= "<option value=\"".$myrepono_array['myr_cache']['domains'][$myrepono_domains_key]['id']."\"";
								if ($myrepono_array['myr_cache']['domains'][$myrepono_domains_key]['id']==$myrepono_domain_id) {
									$myrepono_select_domains .= " selected";
									$myrepono_domains_name_title = " for $myrepono_domains_name";
								}

								$myrepono_domains_name_count++;

								$myrepono_select_domains .= ">$myrepono_domains_name_count. $myrepono_domains_name &nbsp;</option>";

							}
						}
					}
				}



				print<<<END

	<h3>Your WordPress Backups$myrepono_domains_name_title</h3>

	<p>Your backups are listed below and can be viewed, downloaded, archived and restored via myRepono.com.</p>

<table class="widefat">
<thead>
    <tr>
        <th width="16"></th>
        <th>Timestamp</th>
        <th>Location</th>
        <th>Size</th>
        <th width="120">Actions</th>
    </tr>
</thead>
$myrepono_backups_footer
<tbody>
$myrepono_backups
</tbody>
</table>

<br>
<p><small>Note: All data is sourced from myRepono.com, as such page loading may be delayed when caching data.  Data is cached for 1 or 5 minutes depending on importance, this means it may take up to 5 minutes for changes to appear.</small></p>

</td><td width="30" style="width:30px;">&nbsp;</td><td width="35%" valign="top">
END;

				if (count($myrepono_array['myr_cache']['domains'])>1) {

					if ($myrepono_select_domains!="") {

						print <<<END
<table class="widefat">
<thead>
    <tr>
        <th><b>Your Domains</b></th>
    </tr>
<tbody>
   <tr>
     <td>

		<form name="form" action="options-general.php?page=myrepono" method="post">

		<table class="form-table">
			<tbody>
			<tr>
				<th>
					<label><b>Select Domain:</b></label>
				</th>
				<td width="50%">
				<select name="myrepono_select_domains" id="myrepono_select_domains" class="regular-text code">
				$myrepono_select_domains
				</select>
				</td>
			</tr>
			</tbody></table>

		<p class="submit" style="margin-bottom:6px;padding-bottom:0px;">
			<input name="submit" class="button-primary" value="View Domain" type="submit">
		</p>

		</form>


     </td>
   </tr>
</tbody>
</table>
<br>

END;

					}


				}

				print <<<END
<table class="widefat">
<thead>
    <tr>
        <th><b>Your Backup Configuration</b></th>
    </tr>
<tbody>
   <tr>
     <td>

		<form name="form" action="options-general.php?page=myrepono" method="post">



		<p>Your backup configuration dictates how frequently your WordPress installation will be backed up, and also how the backups will be stored.</p>



		<table class="form-table">
			<tbody>
			<tr>
				<th>
					<label><b>Backup Frequency:</b></label>
				</th>
				<td width="50%">
				$myrepono_select_frequency
				</td>
			</tr>
			<tr>
				<th>
					<label><b>Stored Backups:</b></label>
				</th>
				<td>
				<select name="myrepono_stored" id="myrepono_stored" class="regular-text code">
				$myrepono_select_stored
				</select>
				</td>
			</tr>
			<tr>
				<th>
					<label><b>Backup Location:</b></label>
				</th>
				<td>
				$myrepono_select_primary_location
				</td>
			</tr>
$myrepono_select_mirror_location
			<tr>
				<th>
					<label><b>Status:</b></label>
				</th>
				<td>
				<select name="myrepono_active" id="myrepono_active" class="regular-text code">
				<option value="1"$display_status1_checked>Active&nbsp; </option>
				<option value="0"$display_status0_checked>Paused&nbsp; </option>
				</select>
				</td>
			</tr>
			</tbody></table>

		<p class="submit" style="margin-bottom:6px;padding-bottom:0px;">
			<input name="submit" class="button-primary" value="Save Changes" type="submit">
		</p>

		</form>


     </td>
   </tr>
</tbody>
</table>
<br>

<script type="text/javascript"><!--//--><![CDATA[//><!--
function myreponoFrequency(freq) {
if (freq=="6") {
document.getElementById('myrepono_select_frequency_days_div').style.display = "block";
} else {
document.getElementById('myrepono_select_frequency_days_div').style.display = "none";
}
}
previous_location = "$myrepono_select_previous_location";
function myreponoPrimaryLocation(location) {
if (previous_location!="") {
document.getElementById('myrepono_mirror_location_' + previous_location).style.display = "block";
}
document.getElementById('myrepono_mirror_location_' + location).style.display = "none";
previous_location = location;
}
//--><!]]></script>

<table class="widefat">
<thead>
    <tr>
        <th><b>Your myRepono Account</b></th>
    </tr>
<tbody>
   <tr>
     <td>

		<table class="form-table">
			<tbody>
			<tr>
				<th>
					<label><b>Email Address:</b></label>
				</th>
				<td width="50%">
				$myrepono_username
				</td>
			</tr>
			<tr>
				<th>
					<label><b>Account Balance:</b></label>
				</th>
				<td>
				$myrepono_user_balance
				</td>
			</tr>
			</tbody></table>

		<p class="submit" style="margin-bottom:6px;padding-bottom:0px;">
			<a href="http://myRepono.com/my/billing/topup/" class="button-primary" target="new">Top-Up Balance</a>
			<a href="http://myRepono.com/my/" class="button-primary" target="new">View Account</a>
		</p>


     </td>
   </tr>
</tbody>
</table>

<br>
<table class="widefat">
<thead>
    <tr>
        <th><b>Change myRepono Account</b></th>
    </tr>
<tbody>
   <tr>
     <td>

	<p>Your myRepono plugin is currently configured for your myRepono.com account, <b>$myrepono_username</b>.  Please select the button below to clear your current configuration, this will not disrupt your backups or domain configuration which will remain stored and active in your account on myRepono.com.</p>
	<form name="form" action="options-general.php?page=myrepono" method="post">
	<p class="submit" style="margin-top:6px;padding-top:0px;margin-bottom:6px;padding-bottom:0px;">
		<input name="button" class="button-primary" value="Reset Default Configuration" type="button" onclick="this.style.display='none';document.getElementById('myrepono_reset_config').style.display='block';">
		<input name="submit" class="button-primary" value="Confirm Reset Default Configuration" type="submit" id="myrepono_reset_config" style="display:none;">
	</p>
	</form>


     </td>
   </tr>
</tbody>
</table>
<br>

</td></tr>
</table>

END;



			}

			print<<<END



</div>


END;

		}
	} else {

			print<<<END
<div class="wrap">
<p><b>An error has occurred whilst trying to setup your myRepono configuration data, please <a href="http://myRepono.com/contact/">contact support</a>.</b></p>
</div>
END;


	}
}

function myrepono_validate_email($email) {

  $return = "1";

  if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
    $return = "0";
  }
  $email_array = explode("@", $email);
  $local_array = explode(".", $email_array[0]);
  for ($i = 0; $i < sizeof($local_array); $i++) {
    if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
      $return = "0";
    }
  }
  if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
    $domain_array = explode(".", $email_array[1]);
      if (sizeof($domain_array) < 2) {
        $return = "0";
      }
    for ($i = 0; $i < sizeof($domain_array); $i++) {
      if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
        $return = "0";
      }
    }
  }

  return $return;

}

function myrepono_css() {
	// This makes sure that the posinioning is also good for right-to-left languages
	$x = ( is_rtl() ) ? 'left' : 'right';

	echo "
	<style type='text/css'>
	#myrepono_status {
		position: absolute;
		top: 4.7em;
		margin: 0;
		padding: 4px;
		padding-left:8px;
		padding-right:8px;
		$x: 215px;
		font-size: 10px;
		z-index:2;
		background:#828282;
		color:#fff;
		-moz-border-radius:0px 0px 5px 5px;
		-webkit-border-radius:0px 0px 5px 5px;
	}
	#myrepono_status a {
		color:#fff;
		text-decoration:none;
		position:relative;
		top:-1px;
	}
	#myrepono_logo {
		padding:2px;
		border:1px solid #bbb;
		margin-left:10px;
		margin-right:10px;
	}
	.myrepono_response {
		border-width:1px;
		border-style:solid;
		padding:0 .6em;
		margin:5px 15px 2px;
		-moz-border-radius:3px;
		-khtml-border-radius:3px;
		-webkit-border-radius:3px;
		border-radius:3px;
		background-color:#ffffe0;
		border-color:#e6db55;
	}
	</style>
	";
}


function myrepono_time_ago($date) {

	$granularity = "2";
	$retval = "";

	$myrepono_tmp_timezone = "";
	if (date_default_timezone_get()) {
		$myrepono_tmp_timezone = date_default_timezone_get();
	}

	date_default_timezone_set('America/New_York');

    $date = strtotime($date);
    $difference = time() - $date;
    $periods = array('decade' => 315360000,
        'year' => 31536000,
        'month' => 2628000,
        'week' => 604800,
        'day' => 86400,
        'hour' => 3600,
        'minute' => 60);

    foreach ($periods as $key => $value) {
        if ($difference >= $value) {
            $time = round($difference/$value);
            $difference %= $value;
            $retval .= ($retval ? ' ' : '').$time.' ';
            $retval .= (($time > 1) ? $key.'s' : $key);
            $granularity--;
        }
        if ($granularity == '0') { break; }
    }

    if ($retval=="") {

    	$retval = "1 minute";

    }

    if ($myrepono_tmp_timezone!="") {
    	date_default_timezone_set($myrepono_tmp_timezone);
    }

    return "".$retval."";
}




// The following functions are taken from the myRepono hapi Example Code

// myRepono HostAPI/hapi
// PHP Example of Auto Account and API Setup
// http://myRepono.com/
// Copyright 2010 ionix Limited

// Usage of the HostAPI/hapi requires you have an API key and password.
// If you do not have an API key or password please contact us: http://myRepono.com/contact/

// This is an example script which uses the myRepono HostAPI/hapi to create an account, setup a
// domain within the account, and to setup the myRepono API PHP script on the website.

// API URL: https://myRepono.com/hapi/h.api


function myrepono_hapi_auto_setup($myrepono_hapi_input) {

	// myRepono HostAPI Auto Account and API Setup PHP Function

	$myrepono_hapi = array();
	$myrepono_hapi_return = array();

	global $myrepono_hapi_return;

	$myrepono_hapi['request_url'] = "https://myrepono.com/hapi/h.api?api_key=".$myrepono_hapi_input['api_key']."&api_pass=".$myrepono_hapi_input['api_pass'];

	// Step 1 - Sign-Up

	if (!isset($myrepono_hapi_input['skip_signup'])) {
		$myrepono_hapi_input['skip_signup'] = "";
	}
	$signup_email_sent = "0";

	if ($myrepono_hapi_input['skip_signup']!="1") {

		$myrepono_hapi['request_url_signup'] = $myrepono_hapi['request_url']."&api_action=user-add&email=".rawurlencode($myrepono_hapi_input['email'])."&currency=".rawurlencode($myrepono_hapi_input['currency']);
		$myrepono_hapi['result_signup'] = file_get_contents($myrepono_hapi['request_url_signup']);

		$myrepono_hapi['result_signup'] = explode("|:|\n",$myrepono_hapi['result_signup']);

		if (isset($myrepono_hapi['result_signup'][0])) {

			$myrepono_hapi['result_signup_response'] = explode("|",$myrepono_hapi['result_signup'][0]);

			if ($myrepono_hapi['result_signup_response'][0]!="1") {

				myrepono_hapi_auto_setup_error($myrepono_hapi['result_signup_response'][0], $myrepono_hapi['result_signup_response'][1], $myrepono_hapi['result_signup_response'][2]);

			} else {

				myrepono_hapi_auto_setup_success();

				if ($myrepono_hapi['result_signup_response'][1]=="1") {

					$signup_email_sent = "1";

				} else {

					$signup_email_sent = "0";

				}

			}

		} else {

			myrepono_hapi_auto_setup_error();

		}

	} else {

		$myrepono_hapi['result_password'] = $myrepono_hapi_input['password'];


		// Step 2 - Add Domain

		if (isset($myrepono_hapi['result_password'])) {
			if ($myrepono_hapi['result_password']!="") {

				$myrepono_hapi['request_url_domain'] = $myrepono_hapi['request_url']."&api_action=domain-add&email=".rawurlencode($myrepono_hapi_input['email'])."&password=".rawurlencode($myrepono_hapi['result_password'])."&domain_url=".rawurlencode($myrepono_hapi_input['domain_url'])."myrepono.php";

				$myrepono_hapi['result_domain'] = file_get_contents($myrepono_hapi['request_url_domain']);
				$myrepono_hapi['result_domain'] = explode("|:|\n",$myrepono_hapi['result_domain']);

				if (isset($myrepono_hapi['result_domain'][0])) {

					$myrepono_hapi['result_domain_response'] = explode("|",$myrepono_hapi['result_domain'][0]);

					if ($myrepono_hapi['result_domain_response'][0]!="1") {

						myrepono_hapi_auto_setup_error($myrepono_hapi['result_domain_response'][0], $myrepono_hapi['result_domain_response'][1], $myrepono_hapi['result_domain_response'][2]);

					} else {

						$myrepono_hapi['result_domain_response'] = explode("|",$myrepono_hapi['result_domain'][1]);
						if ($myrepono_hapi['result_domain_response'][0]=="Domain ID") {

							$myrepono_hapi['result_domain_id'] = $myrepono_hapi['result_domain_response'][1];
							$myrepono_hapi_return['domain_id'] = $myrepono_hapi['result_domain_id'];

						} else {

							myrepono_hapi_auto_setup_error($myrepono_hapi['result_domain_response'][0], $myrepono_hapi['result_domain_response'][1], $myrepono_hapi['result_domain_response'][2]);

						}

					}

				} else {

					myrepono_hapi_auto_setup_error();

				}
			}
		}



		// Step 3 - Retrieve myrepono.php Source

		if ((isset($myrepono_hapi['result_password'])) && (isset($myrepono_hapi['result_domain_id']))) {

			if (($myrepono_hapi['result_password']!="") && ($myrepono_hapi['result_domain_id']!="")) {

				$myrepono_hapi['request_url_source'] = $myrepono_hapi['request_url']."&api_action=source&email=".rawurlencode($myrepono_hapi_input['email'])."&password=".rawurlencode($myrepono_hapi['result_password'])."&domain_id=".rawurlencode($myrepono_hapi['result_domain_id']);

				$myrepono_hapi['result_source'] = file_get_contents($myrepono_hapi['request_url_source']);
				$myrepono_hapi['result_source'] = explode("|:|\n",$myrepono_hapi['result_source']);

				if (isset($myrepono_hapi['result_source'][0])) {

					$myrepono_hapi['result_source_response'] = explode("|",$myrepono_hapi['result_source'][0]);

					if ($myrepono_hapi['result_source_response'][0]!="1") {

						myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

					} else {

						if (isset($myrepono_hapi['result_source'][1])) {

							$myrepono_hapi['result_myrepono_source'] = $myrepono_hapi['result_source'][1];

							$myrepono_hapi_input['domain_path_myrepono'] = $myrepono_hapi_input['domain_path']."myrepono.php";

							$myrepono_hapi_file = fopen($myrepono_hapi_input['domain_path_myrepono'], 'w');
							fwrite($myrepono_hapi_file, $myrepono_hapi['result_myrepono_source']);
							fclose($myrepono_hapi_file);

							if (file_exists($myrepono_hapi_input['domain_path_myrepono'])) {

								myrepono_hapi_auto_setup_success();

								$myrepono_hapi_return['password'] = $myrepono_hapi['result_password'];

								// Success, setup is complete.

							} else {

								myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], "API could not be installed.");

							}

						} else {

							myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], "Unable to retrieve API source.");

						}

					}

				} else {

					myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

				}

			}
		}

		// Step 4 - Configure Files & Databases to Backup

		$myrepono_files_added = "0";

		if ((isset($myrepono_hapi['result_password'])) && (isset($myrepono_hapi['result_domain_id']))) {

			if (($myrepono_hapi['result_password']!="") && ($myrepono_hapi['result_domain_id']!="")) {

				$myrepono_hapi['request_url_source'] = $myrepono_hapi['request_url']."&api_action=domain-files&email=".rawurlencode($myrepono_hapi_input['email'])."&password=".rawurlencode($myrepono_hapi['result_password'])."&domain_id=".rawurlencode($myrepono_hapi['result_domain_id'])."&directory=".rawurlencode($myrepono_hapi_input['domain_backup_path']);

				$myrepono_hapi['result_source'] = file_get_contents($myrepono_hapi['request_url_source']);
				$myrepono_hapi['result_source'] = explode("|:|\n",$myrepono_hapi['result_source']);

				if (isset($myrepono_hapi['result_source'][0])) {

					$myrepono_hapi['result_source_response'] = explode("|",$myrepono_hapi['result_source'][0]);

					if ($myrepono_hapi['result_source_response'][0]!="1") {

						myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

					} else {

						$myrepono_files_added = "1";

					}

				} else {

					myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

				}

			}
		}

		$myrepono_dbs_added = "0";

		if ((isset($myrepono_hapi['result_password'])) && (isset($myrepono_hapi['result_domain_id']))) {

			if (($myrepono_hapi['result_password']!="") && ($myrepono_hapi['result_domain_id']!="")) {

				$myrepono_hapi['request_url_source'] = $myrepono_hapi['request_url']."&api_action=domain-dbs&email=".rawurlencode($myrepono_hapi_input['email'])."&password=".rawurlencode($myrepono_hapi['result_password'])."&domain_id=".rawurlencode($myrepono_hapi['result_domain_id'])."&dbhost=".rawurlencode($myrepono_hapi_input['domain_database_host'])."&dbname=".rawurlencode($myrepono_hapi_input['domain_database_name'])."&dbuser=".rawurlencode($myrepono_hapi_input['domain_database_user'])."&dbpass=".rawurlencode($myrepono_hapi_input['domain_database_pass']);

				$myrepono_hapi['result_source'] = file_get_contents($myrepono_hapi['request_url_source']);
				$myrepono_hapi['result_source'] = explode("|:|\n",$myrepono_hapi['result_source']);

				if (isset($myrepono_hapi['result_source'][0])) {

					$myrepono_hapi['result_source_response'] = explode("|",$myrepono_hapi['result_source'][0]);

					if ($myrepono_hapi['result_source_response'][0]!="1") {

						myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

					} else {

						$myrepono_dbs_added = "1";

					}

				} else {

					myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

				}

			}
		}


		// Step 5 - Activate Backup & Set Settings



		$myrepono_settings_saved = "0";

		if ((isset($myrepono_hapi['result_password'])) && (isset($myrepono_hapi['result_domain_id']))) {

			if (($myrepono_hapi['result_password']!="") && ($myrepono_hapi['result_domain_id']!="")) {

				$myrepono_hapi['request_url_source'] = $myrepono_hapi['request_url']."&api_action=domain-settings&email=".rawurlencode($myrepono_hapi_input['email'])."&password=".rawurlencode($myrepono_hapi['result_password'])."&domain_id=".rawurlencode($myrepono_hapi['result_domain_id'])."&set_frequency=3&set_frequency_days=0&set_stored=7&set_active=1&set_primary_location=1";

				$myrepono_hapi['result_source'] = file_get_contents($myrepono_hapi['request_url_source']);
				$myrepono_hapi['result_source'] = explode("|:|\n",$myrepono_hapi['result_source']);

				if (isset($myrepono_hapi['result_source'][0])) {

					$myrepono_hapi['result_source_response'] = explode("|",$myrepono_hapi['result_source'][0]);

					if ($myrepono_hapi['result_source_response'][0]!="1") {

						myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

					} else {

						$myrepono_settings_saved = "1";

					}

				} else {

					myrepono_hapi_auto_setup_error($myrepono_hapi['result_source_response'][0], $myrepono_hapi['result_source_response'][1], $myrepono_hapi['result_source_response'][2]);

				}

			}
		}
	}


	return $myrepono_hapi_return;

}

function myrepono_hapi_auto_setup_error($myrepono_hapi_success = "0", $myrepono_hapi_error_code = "", $myrepono_hapi_error_msg = "") {

	// myRepono HostAPI Error Function

	global $myrepono_hapi_return;

	$myrepono_hapi_return['result'] = "0";
	if (($myrepono_hapi_success=="0") && ($myrepono_hapi_error_code!="")) {
		$myrepono_hapi_return['error_code'] = $myrepono_hapi_error_code;
		$myrepono_hapi_return['error_msg'] = $myrepono_hapi_error_msg;
	} else {
		$myrepono_hapi_return['error_code'] = "";
		$myrepono_hapi_return['error_msg'] = "An error occurred.";
	}

	return;

}

function myrepono_hapi_auto_setup_success() {

	// myRepono HostAPI Success Function

	global $myrepono_hapi_return;

	$myrepono_hapi_return['result'] = "1";
	$myrepono_hapi_return['error_code'] = "";
	$myrepono_hapi_return['error_msg'] = "";

	return;

}


add_action('admin_footer', 'myrepono');

add_action('admin_menu', 'myrepono_menu');

add_action('admin_head', 'myrepono_css');

?>
