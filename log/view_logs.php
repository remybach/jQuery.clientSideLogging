<?php

define('LOG_FILE', dirname(__FILE__) . '/log.txt');

$log_json = file_get_contents(LOG_FILE);
$logs = json_decode($log_json);
if ( !$logs ) {
	$logs = array();
} else {
	// We don't care about hashes on the frontend, and this lets us sort.
	$logs->incidence = get_object_vars($logs->incidence);

	function format_log($log) {
		$log->decoded_message = $log->message;
		if ( $log->format == 'json' ) {
			$log->decoded_message = print_r(json_decode($log->message), 1);
			$log->decoded_message = preg_replace('/stdClass Object\s*/', '', $log->decoded_message);
			$log->decoded_message = htmlentities($log->decoded_message);
			$log->decoded_message = '<pre>' . $log->decoded_message . '</pre>';
		}
	}

	foreach ( (array) $logs->incidence as $log ) {
		$log = format_log($log);
	}

	foreach ( (array) $logs->log as $log ) {
		$log = format_log($log);
	}

	// Show log entries in reverse chronological order.
	$logs->log = array_reverse($logs->log);

	// Sort incidence entries by count.
	usort(
		$logs->incidence,
		function($a, $b) {
			if ( $a->count == $b->count ) {
				// If the counts are the same, sort by most recent.
				if ( $a->first_logged == $b->first_logged ) {
					return 0;
				}
				return ( $a->first_logged < $b->first_logged ) ? 1 : -1;
			}
			// We're sorting descending.
			return ( $a->count < $b->count ) ? 1 : -1;
		}
	);

	// If we've been given a type, filter by type; otherwise display everything.
	if ( !empty($_REQUEST['type']) ) {
		$filter_type = function($log) {
			return ( $log->type == $_REQUEST['type'] );
		};

		$logs->incidence = array_filter(
			$logs->incidence,
			$filter_type
		);

		$logs->log = array_filter(
			$logs->log,
			$filter_type
		);
	}
}

?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title></title>
  <meta name="description" content="">
  <meta name="author" content="">

  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- CSS concatenated and minified via ant build script-->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
  <!-- end CSS-->
</head>

<body>
	<div id="container">
		<header class="row-fluid navbar">
			<div class="navbar-inner">
				<div class="container">
					<h1 class="brand">Client Side Logs</h1>
					<ul class="nav">
						<li<?php echo empty($_REQUEST['type']) ? ' class="active"' : ''; ?>><a href="view_logs.php">All</a></li>

						<?php foreach ( array('error', 'info', 'log') as $type ) : ?>
						<?php $active = ( !empty($_REQUEST['type']) && $type == $_REQUEST['type'] ) ? ' active' : ''; ?>
						<li class="<?php echo $type; ?><?php echo $active ?>"><a href="?type=<?php echo $type ?>"><?php echo ucwords($type) ?></a></li>
						<?php endforeach ?>
					</ul>
					<form class="navbar-search pull-right">
						<input type="text" placeholder="regex filter">
					</form>
				</div>
			</div>
		</header>
		<div id="main-container" class="row-fluid">
			<?php if (!empty($logs)): ?>
				<div class="span12">
					<h3>Overview</h3>
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<?php if (empty($_REQUEST['type'])): ?>
									<th class="span1">type</th>
								<?php endif ?>
								<th class="span1">#</th>
								<th class="span<?php echo empty($_REQUEST['type']) ? '10' : '11'; ?>">Details</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $logs->incidence as $log ) : ?>
							<tr>
								<?php if (empty($_REQUEST['type'])): ?>
								<td class="type <?php echo $log->type ?>"><?php echo $log->type ?></td>
								<?php endif; ?>
								<td><?php echo $log->count ?></td>
								<td>
									<?php echo $log->decoded_message ?>
								</td>
							</tr>
							<?php endforeach ?>
						</tbody>
					</table>

					<h3>All entries</h3>
					<table class="table table-bordered table-striped">
						<thead>
							<?php if (empty($_REQUEST['type'])): ?>
								<th class="span1">type</th>
							<?php endif ?>
							<th class="span2">Time</th>
							<th>Message</th>
						</thead>
						<tbody>
							<?php foreach ( $logs->log as $log ) : ?>
							<tr>
								<?php if (empty($_REQUEST['type'])): ?>
								<td class="type <?php echo $log->type ?>"><?php echo $log->type ?></td>
								<?php endif; ?>
								<td><?php echo $log->time ?></td>
								<td><?php echo $log->decoded_message ?></td>
							</tr>
							<?php endforeach ?>
						</tbody>
					</table>

					<?php /*
					<div class="pagination">
						<ul>
							<li><a href="#">Prev</a></li>
							<li class="active"><a href="#">1</a></li>
							<li><a href="#">2</a></li>
							<li><a href="#">3</a></li>
							<li><a href="#">4</a></li>
							<li><a href="#">Next</a></li>
						</ul>
					</div>
					*/ ?>
				</div>
		<?php else: ?>
			<div class="span12">
				<h3>No logs available as of yet.</h3>
			</div>
		<?php endif; ?>
		</div>
	</div> <!--! end of #container -->


	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script src="js/view_logs.js" type="text/javascript" charset="utf-8"></script>

	<!--[if lt IE 7 ]>
	<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
	<script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
	<![endif]-->
</body>
</html>
