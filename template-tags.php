<?php

### Function: Display UserOnline
function users_online() {
	echo get_users_online();
}

function get_users_online() {
	$template = UserOnline_Core::$templates['useronline'];
	$template = str_ireplace('%USERONLINE_PAGE_URL%', UserOnline_Core::$options->page_url, $template);
	$template = str_ireplace('%USERONLINE_MOSTONLINE_COUNT%', get_most_users_online(), $template);
	$template = str_ireplace('%USERONLINE_MOSTONLINE_DATE%', get_most_users_online_date(), $template);

	return UserOnline_Template::format_count(get_users_online_count(), 'user', $template);
}

### Function: Display UserOnline Count
function users_online_count() {
	echo number_format_i18n(get_useronline_count());
}

function get_users_online_count() {
	return UserOnline_Core::get_user_online_count();
}

### Function: Display Max UserOnline
function most_users_online() {
	echo number_format_i18n(get_most_users_online());
}

function get_most_users_online() {
	return intval(UserOnline_Core::$most->count);
}

### Function: Display Max UserOnline Date
function most_users_online_date() {
	echo get_most_users_online_date();
}

function get_most_users_online_date() {
	return UserOnline_Template::format_date(UserOnline_Core::$most->date);
}

### Function: Display Users Browsing The Site
function users_browsing_site() {
	echo get_users_browsing_site();
}

function get_users_browsing_site() {
	global $wpdb;

	$users_online = $wpdb->get_results("SELECT * FROM $wpdb->useronline");

	return UserOnline_Template::compact_list('site', $users_online);
}

### Function: Display Users Browsing The (Current) Page
function users_browsing_page($page_url = '') {
	echo get_users_browsing_page($page_url);
}

function get_users_browsing_page($page_url = '') {
	global $wpdb;

	if ( empty($page_url) )
		$page_url = $_SERVER['REQUEST_URI'];

	$users_online = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->useronline WHERE page_url = %s", $page_url));

	return UserOnline_Template::compact_list('page', $users_online);
}

### Function: UserOnline Page
function users_online_page() {
	global $wpdb;

	$usersonline = $wpdb->get_results("SELECT * FROM $wpdb->useronline");

	$user_buckets = array();
	foreach ( $usersonline as $useronline )
		$user_buckets[$useronline->user_type][] = $useronline;

	$counts = UserOnline_Template::get_counts($user_buckets);

	$nicetexts = array();
	foreach ( array('user', 'member', 'guest', 'bot') as $user_type )
		$nicetexts[$user_type] = UserOnline_Template::format_count($counts[$user_type], $user_type);

	$text = _n(
		'There is <strong>%s</strong> online now: <strong>%s</strong>, <strong>%s</strong> and <strong>%s</strong>.',
		'There are a total of <strong>%s</strong> online now: <strong>%s</strong>, <strong>%s</strong> and <strong>%s</strong>.',
		$counts['user'], 'wp-useronline'
	);

	$output = 
	 html('p', vsprintf($text, $nicetexts))
	.html('p', UserOnline_Template::format_most_users())
	.UserOnline_Template::detailed_list($counts, $user_buckets, $nicetexts);

	return apply_filters('useronline_page', $output);
}

### Function Check If User Is Online
function is_user_online($user_id) {
	global $wpdb;

	return (bool) $wpdb->get_var($wpdb-prepare("SELECT COUNT(*) FROM $wpdb->useronline WHERE user_id = %d LIMIT 1", $user_id));
}


class UserOnline_Template {

	function compact_list($user_type, $users) {
		if ( empty($users) )
			return '';

		$buckets = array();
		foreach ( $users as $user )
			$buckets[$user->user_type][] = $user;

		$counts = self::get_counts($buckets);

		// Template - Naming Conventions
		$naming = UserOnline_Core::$$naming->get();

		// Template - User(s) Browsing Site
		list($separator_members, $separator_guests, $separator_bots, $template) = UserOnline_Core::$templates->get("browsing$user_type");

		// Nice Text For Users
		$template = self::format_count($counts['user'], 'user', $template);

		// Print Member Name
		$temp_member = '';
		$members = $buckets['member'];
		if ( $members ) {
			$temp_member = array();
			foreach ( $members as $member )
				$temp_member[] = self::format_name($member);
			$temp_member = implode($separator_members, $temp_member);
		}
		$template = str_ireplace('%USERONLINE_MEMBER_NAMES%', $temp_member, $template);

		// Counts
		foreach ( array('member', 'guest', 'bot') as $user_type ) {
			if ( $counts[$user_type] > 1 )
				$number = str_ireplace('%USERONLINE_COUNT%', number_format_i18n($counts[$user_type]), $naming[$user_type . 's']);
			elseif ( $counts[$user_type] == 1 )
				$number = $naming[$user_type];
			else
				$number = '';
			$template = str_ireplace("%USERONLINE_{$user_type}S%", $number, $template);
		}

		// Seperators
		if ( $counts['member'] > 0 && $counts['guest'] > 0 )
			$separator = $separator_guests;
		else
			$separator = '';
		$template = str_ireplace('%USERONLINE_GUESTS_SEPERATOR%', $separator, $template);

		if ( ($counts['guest'] > 0 || $counts['member'] > 0 ) && $counts['bot'] > 0)
			$separator = $separator_bots;
		else
			$separator = '';
		$template = str_ireplace('%USERONLINE_BOTS_SEPERATOR%', $separator, $template);

		return $template;
	}

	function detailed_list($counts, $user_buckets, $nicetexts) {
		if ( $counts['user'] == 0 )
			return html('h2', __('No one is online now.', 'wp-useronline'));

		$on = __('on', 'wp-useronline');
		$url = __('url', 'wp-useronline');
		$referral = __('referral', 'wp-useronline');

		$output = '';
		foreach ( array('member', 'guest', 'bot') as $user_type ) {
			if ( !$counts[$user_type] )
				continue;

			$count = $counts[$user_type];
			$users = $user_buckets[$user_type];
			$nicetext = $nicetexts[$user_type];

			$output .= html('h2', $nicetext . ' ' . __('Online Now', 'wp-useronline'));

			$i=1;
			foreach ( $users as $user ) {
				$nr = number_format_i18n($i++);
				$name = self::format_name($user);
				$user_ip = self::format_ip($user->user_ip);
				$date = self::format_date($user->timestamp);
				$page_title = $user->page_title;
				$current_link = '[' . html_link(esc_url($user->page_url), $url) . ']';

				$referral_link = '';
				if ( !empty($user->referral) )
					$referral_link = '[' . html_link(esc_url($user->referral), $referral) . ']';

				$output .= "<p><strong>#$nr - $name</strong> $user_ip $on $date<br/>$page_title $current_link $referral_link</p>\n";
			}
		}

		return $output;
	}


	function format_ip($ip) {
		if ( ! current_user_can('administrator') || empty($ip) || $ip == 'unknown' )
			return;

		return '<span dir="ltr">(<a href="http://whois.domaintools.com/' . $ip . '" title="'.gethostbyaddr($ip).'">' . $ip . '</a>)</span>';
	}

	function format_date($date) {
		return mysql2date(sprintf(__('%s @ %s', 'wp-useronline'), get_option('date_format'), get_option('time_format')), $date, true);
	}

	function format_name($user) {
		return apply_filters('useronline_display_name', $user->user_name, $user);
	}

	function format_count($count, $user_type, $template = '') {
		$i = ($count == 1) ? '' : 's';
		$string = UserOnline_Core::$naming->get($user_type . $i);

		$output = str_ireplace('%USERONLINE_COUNT%', number_format_i18n($count), $string);

		if ( empty($template) )
			return $output;

		return str_ireplace('%USERONLINE_USERS%', $output, $template);
	}

	function format_most_users() {
		return sprintf(__('Most users ever online were <strong>%s</strong>, on <strong>%s</strong>', 'wp-useronline'),
			number_format_i18n(get_most_users_online()),
			get_most_users_online_date()
		);
	}

	function get_counts($buckets) {
		$counts = array();
		$total = 0;
		foreach ( array('member', 'guest', 'bot') as $user_type )
			$total += $counts[$user_type] = count(@$buckets[$user_type]);

		$counts['user'] = $total;

		return $counts;
	}
}

