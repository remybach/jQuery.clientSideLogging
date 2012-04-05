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
		<header class="row-fluid">
			<h1 class="span12">Client Side Logging Test</h1>
		</header>
		<div id="main-container" class="row-fluid">
			<aside class="span4">
				<ul>
					<li>
						<input type="checkbox" id="location" name="location" value="">
						<label for="location">Location</label>
					</li>
					<li>
						<input type="checkbox" id="screen_size" name="screen_size" value="">
						<label for="screen_size">Screen Size</label>
					</li>
					<li>
						<input type="checkbox" id="user_agent" name="user_agent" value="">
						<label for="user_agent">User Agent String</label>
					</li>
					<li>
						<input type="checkbox" id="window_size" name="window_size" value="">
						<label for="window_size">Window Size</label>
					</li>
					<li>
						<label for="error_msg"><input type="text" name="error_msg" value=""></label>
						<input type="button" class="error" value="Send error">
					</li>
					<li>
						<label for="info_msg"><input type="text" name="info_msg" value=""></label>
						<input type="button" class="info" value="Send info">
					</li>
					<li>
						<label for="log_msg"><input type="text" name="log_msg" value=""></label>
						<input type="button" class="log" value="Send log">
					</li>
				</ul>
			</aside>
			<div id="main" role="main" class="span8">
				<section class="error">
					<header>
						<h1>Error Log</h1>
					</header>
					<pre class="log_info"><?php echo file_get_contents('/test/log/error.txt'); ?></pre>
				</section>
				<section class="info">
					<header>
						<h1>Info Log</h1>
					</header>
					<pre class="log_info"><?php echo file_get_contents('/test/log/info.txt'); ?></pre>
				</section>
				<section class="log">
					<header>
						<h1>Standard Log</h1>
					</header>
					<pre class="log_info"><?php echo file_get_contents('/test/log/log.txt'); ?></pre>
				</section>
			</div>
		</div>
	</div> <!--! end of #container -->


	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="js/libs/jquery-1.6.2.min.js"><\/script>')</script>


	<!-- scripts concatenated and minified via ant build script-->
	<script defer src="../jQuery.clientSideLogging.js"></script>
	<script defer src="js/scripts.js"></script>
	<!-- end scripts-->


	<script> // Change UA-XXXXX-X to be your site's ID
		// window._gaq = [['_setAccount','UAXXXXXXXX1'],['_trackPageview'],['_trackPageLoadTime']];
		// Modernizr.load({
		// 	load: ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js'
		// });
	</script>

	<!--[if lt IE 7 ]>
	<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
	<script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
	<![endif]-->
</body>
</html>
