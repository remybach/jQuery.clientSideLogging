<?php

define('LOG_FILE', dirname(__FILE__) . '/log.txt');
define('ACCESS_FILE', dirname(__FILE__) . '/access.txt');

// Only allow one entry per … seconds.
define('THROTTLE_TIME', 60);

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

// Initially, assume that this client hasn't logged anything in the last minute.
$has_accessed = false;

if ( !file_exists(ACCESS_FILE) ) {
	if ( is_writable(dirname(ACCESS_FILE)) ) {
		touch(ACCESS_FILE);
	} else {
		die;
	}
}

$accesses = file(ACCESS_FILE);

// Clear out old access logs.
$accesses = array_filter(
	$accesses,
	function($access) {
		global $has_accessed;

		list($date, $ip) = explode("\t", $access);

		$date = trim($date);
		$ip = trim($ip);

		$fresh = ( strtotime($date) + THROTTLE_TIME > time() );

		// If the user has accessed within the last minute, proceed with the
		// filtering — but we won't be logging anything later.
		if ( $fresh && $ip == IP_ADDRESS ) {
			$has_accessed = true;
		}

		return $fresh;
	}
);

// Log an access from this IP.
if ( !$has_accessed ) {
	$accesses[] = date('Y-m-d H:i:s') . "\t" . IP_ADDRESS;
}

file_put_contents(ACCESS_FILE, join("\n", $accesses));

// If the user has submitted a log in the last minute, bail out silently.
if ( $has_accessed ) {
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
	'hash'    => sha1("$type:$message")
);

$log_entry = json_encode($entry);

// First, increment our hitrate log for this entry.
if ( empty($log->incidence->{$entry['hash']}) ) {
	$log->incidence->{$entry['hash']} = (object) array(
		'message' => $entry['message'],
		'count'   => 1
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

