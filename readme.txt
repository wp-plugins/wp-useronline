-> Useronline Plugin For WordPress
--------------------------------------------------
Author	-> Lester 'GaMerZ' Chan
Email	-> lesterch@singnet.com.sg
Website	-> http://www.lesterchan.net/
Demo	-> http://www.lesterchan.net/wp-useronline.php
Updated	-> 29th April 2005
--------------------------------------------------


-> Installation Instructions
--------------------------------------------------
// Open wp-settings.php

Find:
------------------------------------------------------------------
$wpdb->postmeta					= $table_prefix . 'postmeta';
------------------------------------------------------------------
Add Below It:
------------------------------------------------------------------
$wpdb->useronline					= $table_prefix . 'useronline';
------------------------------------------------------------------


// Open wp-admin folder

Put:
------------------------------------------------------------------
useronline-install.php
------------------------------------------------------------------


// Open root Wordpress folder

Put:
------------------------------------------------------------------
wp-useronline.php
------------------------------------------------------------------


// Open wp-content/plugins folder

Put:
------------------------------------------------------------------
useronline.php
------------------------------------------------------------------


// Activate useronline plugin


// Run wp-admin/useronline-install.php

Note:
------------------------------------------------------------------
If You See A Blank Page Means It Is Successfully
------------------------------------------------------------------


// Open wp-content/themes/<YOUR THEME NAME>/header.php 

Add:
------------------------------------------------------------------
<p align="center"><a href="wp-useronline.php"><?php get_useronline(); ?></a></p>
------------------------------------------------------------------