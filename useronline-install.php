<?php
/*
 * Useronline Plugin For WordPress
 *	- useronline-install.php
 *
 * Copyright © 2004-2005 Lester "GaMerZ" Chan
*/


// Require WordPress Config
require_once('../wp-config.php');

// Create Useronline Table
$sql[] = "CREATE TABLE $wpdb->useronline (".
  " `timestamp` int(15) NOT NULL default '0',".
  " `username` varchar(50) NOT NULL default '',".
  " `ip` varchar(40) NOT NULL default '',".
  " `location` varchar(255) NOT NULL default '',".
  " `url` varchar(255) NOT NULL default '',".
  " PRIMARY KEY  (`timestamp`),".
  " KEY `username` (`username`),".
  " KEY `ip` (`ip`),".
  " KEY `file` (`location`))";

// Run The Queries
foreach($sql as $query) {
	$wpdb->query($query);
}
?>