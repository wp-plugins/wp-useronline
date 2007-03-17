<?php
/*
 * Useronline Plugin For WordPress
 *	- wp-useronline.php
 *
 * Copyright © 2004-2005 Lester "GaMerZ" Chan
*/


// Require WordPress Header
require('wp-blog-header.php');

// Search Bots
$bots = array('Google Bot' => 'googlebot', 'MSN' => 'msnbot', 'Alex' => 'ia_archiver', 'Lycos' => 'lycos', 'Ask Jeeves' => 'askjeeves', 'Altavista' => 'scooter', 'AllTheWeb' => 'fast-webcrawler', 'Inktomi' => 'slurp@inktomi', 'Turnitin.com' => 'turnitinbot');

// Reassign Bots Name
$bots_name = array();
foreach($bots as $botname => $botlookfor) {
	$bots_name[] = $botname;
}

// Get User Online
$usersonline = $wpdb->get_results("SELECT * FROM $wpdb->useronline");

// Type Of Users Array
$bots = array();
$guests = array();
$members = array();

// Users Count
$total = array();
$total['bots'] = 0;
$total['guests'] = 0;
$total['members'] = 0;

// Assign It To Array
foreach($usersonline as $useronline) {
	if($useronline->username == 'Guest') {
		$guests[] = array('username' => stripslashes($useronline->username), 'timestamp' => $useronline->timestamp, 'ip' => $useronline->ip, 'location' => stripslashes($useronline->location), 'url' => $useronline->url);
		$total['guests']++;
	} elseif(in_array($useronline->username, $bots_name)) {
		$bots[] = array('username' => stripslashes($useronline->username), 'timestamp' => $useronline->timestamp, 'ip' => $useronline->ip, 'location' => stripslashes($useronline->location), 'url' => $useronline->url);
		$total['bots']++;
	} else {
		$members[] = array('username' => stripslashes($useronline->username), 'timestamp' => $useronline->timestamp, 'ip' => $useronline->ip, 'location' => stripslashes($useronline->location), 'url' => $useronline->url);
		$total['members']++;
	}
}

// Nicer Text
$nicetext = array();
if($total['bots'] > 1) { $nicetext['bots'] = 'Bots'; } else {	$nicetext['bots'] = 'Bot'; }
if($total['guests'] > 1) { $nicetext['guests'] = 'Guests'; } else { $nicetext['guests'] = 'Guest'; }
if($total['members'] > 1) { $nicetext['members'] = 'Members'; } else { $nicetext['members'] = 'Member'; }

// Check IP
function check_ip($ip) {
	if(isset($_COOKIE['wordpressuser_'.COOKIEHASH])) {
		return "(<a href=\"http://ws.arin.net/cgi-bin/whois.pl?queryinput=$ip\" target=\"_blank\" title=\"".gethostbyaddr($ip)."\">$ip</a>)";
	}
}
?>
<?php get_header(); ?>
	<div id="content" class="narrowcolumn">
		<p>There Are A Total Of <b><?=$total['members'].' '.$nicetext['members']?></b>, <b><?=$total['guests'].' '.$nicetext['guests']?></b> And <b><?=$total['bots'].' '.$nicetext['bots']?></b> Online Now.<b></b> </p>
		<table width="100%" border="0" cellspacing="1" cellpadding="5">
		<?php 
				if($total['members'] > 0) {
					echo 	'<tr><td><h2 class="pagetitle">'.$total['members'].' '.$nicetext['members'].' Online Now</h2></td></tr>';
				}
		?>
				<?php
					$no=1;
					foreach($members as $member) {
						echo '<tr>';
						echo '<td><b>#'.$no.' - <a href="wp-stats.php?author='.$member['username'].'">'.$member['username'].'</a></b> '.check_ip($member['ip']).' on '.gmdate('d.m.Y @ H:i',($member['timestamp']+(get_settings('gmt_offset') * 3600))).'<br />'.$member['location'].' [<a href="'.$member['url'].'">url</a>]</td>'."\n";
						echo '</tr>';
						$no++;
					}
					// Print Out Guest
					if($total['guests'] > 0) {
						echo 	'<tr><td><h2 class="pagetitle">'.$total['guests'].' '.$nicetext['guests'].' Online Now</h2></td></tr>';
					}
					$no=1;
					foreach($guests as $guest) {
						echo '<tr>';
						echo '<td><b>#'.$no.' - '.$guest['username'].'</b> '.check_ip($guest['ip']).' on '.gmdate('d.m.Y @ H:i',($guest['timestamp']+(get_settings('gmt_offset') * 3600))).'<br />'.$guest['location'].' [<a href="'.$guest['url'].'">url</a>]</td>'."\n";
						echo '</tr>';
						$no++;
					}
					// Print Out Bots
					if($total['bots'] > 0) {
						echo 	'<tr><td><h2 class="pagetitle">'.$total['bots'].' '.$nicetext['bots'].' Online Now</h2></td></tr>';
					}
					$no=1;
					foreach($bots as $bot) {
						echo '<tr>';
						echo '<td><b>#'.$no.' - '.$bot['username'].'</b> '.check_ip($bot['ip']).' on '.gmdate('d.m.Y @ H:i',($bot['timestamp']+(get_settings('gmt_offset') * 3600))).'<br />'.$bot['location'].' [<a href="'.$bot['url'].'">url</a>]</td>'."\n";
						echo '</tr>';
						$no++;
					}
				?>
				</table>
	</div>
<?php
	get_sidebar();
	get_footer();
?>