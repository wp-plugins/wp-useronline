<?php
/*
Plugin Name: Useronline
Plugin URI: http://www.lesterchan.net/portfolio/programming.php
Description: Adds A Useronline Feature To WordPress
Version: 1.5
Author: GaMerZ
Author URI: http://www.lesterchan.net
*/


### Get IP
function get_IP() {
	if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		$ip_address = $_SERVER["REMOTE_ADDR"];
	} else {
		$ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	if(strpos($ip_address, ',') !== false) {
		$ip_address = explode(',', $ip_address);
		$ip_address = $ip_address[0];
	}
	return $ip_address;
}

### Useronline Function
function get_useronline($user = 'User', $users = 'Users', $usertimeout = 300) {
	global $wpdb;
	// Search Bots
	$bots = array('Google Bot' => 'googlebot', 'MSN' => 'msnbot', 'Alex' => 'ia_archiver', 'Lycos' => 'lycos', 'Ask Jeeves' => 'askjeeves', 'Altavista' => 'scooter', 'AllTheWeb' => 'fast-webcrawler', 'Inktomi' => 'slurp@inktomi', 'Turnitin.com' => 'turnitinbot');

	// Useronline Settings
	$timeoutseconds = $usertimeout;
	$timestamp = time();
	$timeout = $timestamp-$timeoutseconds;
	
	// Check Members
	if(isset($_COOKIE['comment_author_'.COOKIEHASH]))  {
		$memberonline = trim($_COOKIE['comment_author_'.COOKIEHASH]);
		$where = "WHERE username='$memberonline'";
	// Check Guests
	} else { 
		$memberonline = 'Guest';
		$where = "WHERE ip='".get_IP()."'";
	}
	// Check For Bot
	foreach ($bots as $name => $lookfor) { 
		if (stristr($_SERVER['HTTP_USER_AGENT'], $lookfor) !== false) { 
			$memberonline = addslashes($name);
			$where = "WHERE ip='".get_IP()."'";
		} 
	} 
	// Update User First
	$make_page = wp_title('&raquo;', false);
	if(empty($make_page)) { 
		$make_page = get_bloginfo('name');
	} else {
		$make_page = get_bloginfo('name').' &raquo; Blog Archive'.$make_page;
	}
	$update_user = $wpdb->query("UPDATE $wpdb->useronline SET timestamp = '$timestamp', ip = '".get_IP()."', location = '".addslashes($make_page)."', url = '".$_SERVER['REQUEST_URI']."' $where");
	// If No User Insert It
	if(!$update_user) {
		$insert_user = $wpdb->query("INSERT INTO $wpdb->useronline VALUES ('$timestamp', '$memberonline', '".get_IP()."', '".addslashes($make_page)."', '".$_SERVER['REQUEST_URI']."')");
	}

	$delete_users = $wpdb->query("DELETE FROM $wpdb->useronline WHERE timestamp < $timeout");
	$useronline = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->useronline");
	if($useronline > 1) {
		echo "<b>$useronline</b> $users Online";
	} else {
		echo "<b>$useronline</b> $user Online";
	}
}
?>