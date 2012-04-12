<?php

function stripslashes_deep($value) {
	if ( is_array($value) ) {
		$value = array_map('stripslashes_deep', $value);
	} elseif ( is_object($value) ) {
		$vars = get_object_vars( $value );
		foreach ($vars as $key=>$data) {
			$value->{$key} = stripslashes_deep( $data );
		}
	} else {
		$value = stripslashes($value);
	}

	return $value;
}

if ( get_magic_quotes_gpc() ) {
	$_POST    = array_map('stripslashes_deep', $_POST);
	$_GET     = array_map('stripslashes_deep', $_GET);
	$_COOKIE  = array_map('stripslashes_deep', $_COOKIE);
	$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}

define('LOG_FILE', dirname(__FILE__) . '/log.txt');
define('ACCESS_FILE', dirname(__FILE__) . '/access.txt');

// Only allow X entries per Y seconds. The default is to allow 15 entries
// every 2 minutes.
define('THROTTLE_COUNT', 15);
define('THROTTLE_TIME', 120);

define('IP_ADDRESS', $_SERVER['REMOTE_ADDR']);

if ( empty($_REQUEST['msg']) ) {
	die;
}
$message = $_REQUEST['msg'];

// If the error severity isn't specified, assume the lowest.
$type = 'info';
if ( !empty($_REQUEST['type']) ) {
	$type = $_REQUEST['type'];
}

// Accept different formats of message; for now, that's plain-text and JSON.
$formats = array('text', 'json');
$format = 'text';
if ( !empty($_REQUEST['format']) && in_array($_REQUEST['format'], $formats) ) {
	$format = $_REQUEST['format'];
}

if ( !file_exists(ACCESS_FILE) ) {
	if ( is_writable(dirname(ACCESS_FILE)) ) {
		touch(ACCESS_FILE);
	} else {
		die;
	}
}

$accesses = file(ACCESS_FILE);

// Clear out old access logs.
$access_count = 0;
$accesses = array_filter(
	$accesses,
	function($access) {
		global $access_count;

		if ( trim($access) == '' ) {
			return false;
		}

		list($date, $ip) = explode("\t", $access);

		$date = trim($date);
		$ip = trim($ip);

		// Keep entries for twice as long as the throttle time…
		$to_keep = ( strtotime($date) + ( THROTTLE_TIME * 2 ) > time() );
		// …but only consider ones within the throttle time.
		$to_consider = ( strtotime($date) + THROTTLE_TIME > time() );

		// If the user has accessed within the last minute, proceed with the
		// filtering — but we won't be logging anything later.
		if ( $to_consider && $ip == IP_ADDRESS ) {
			$access_count++;
		}

		return $to_keep;
	}
);

// Log an access from this IP.
if ( $access_count < THROTTLE_COUNT ) {
	$accesses[] = date('Y-m-d H:i:s') . "\t" . IP_ADDRESS;
}

file_put_contents(ACCESS_FILE, join("\n", $accesses));

// If the user has been throttled, bail out silently.
if ( $access_count >= THROTTLE_COUNT ) {
	die;
}

// Nonsense over! Let's log this baby.
if ( !file_exists(LOG_FILE) ) {
	if ( is_writable(dirname(LOG_FILE)) ) {
		touch(LOG_FILE);
	} else {
		die;
	}
}

$log = json_decode(file_get_contents(LOG_FILE));

if ( empty($log) ) {
	$log = (object) array(
		'incidence' => (object) array(),
		'log'       => array()
	);
}

$entry = array(
	'time'    => date('Y-m-d H:i:s'),
	'message' => $message,
	'type'    => $type,
	'format'  => $format,
	'hash'    => sha1("$type:$message")
);

$log_entry = json_encode($entry);

// First, increment our hitrate log for this entry.
if ( empty($log->incidence->{$entry['hash']}) ) {
	$log->incidence->{$entry['hash']} = (object) array(
		'first_logged' => $entry['time'],
		'message'      => $entry['message'],
		'type'         => $entry['type'],
		'format'       => $entry['format'],
		'count'        => 1
	);
} else {
	$log->incidence->{$entry['hash']}->count++;
}

// Now, insert a log entry.
if ( empty($log->log) ) {
	$log->log = array();
}

$log->log[] = $entry;

$json_log = json_encode($log);
file_put_contents(LOG_FILE, $json_log);

