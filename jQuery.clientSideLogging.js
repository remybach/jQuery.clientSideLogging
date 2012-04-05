/*
 *	Title: jQuery Client Side Logging Plugin
 *	Author: Rémy Bach
 *	Version: 0.1.0
 *	License: http://remybach.mit-license.org
 *	Url: http://github.com/remybach/jQuery.clientSideLogging
 *	Description:
 *	This plugin allows you to store front end log/info/error messages on the server (note: you will need to have something set up on your server to handle the actual logging).
 *		The idea was born after reading the following article: http://openmymind.net/2012/4/4/You-Really-Should-Log-Client-Side-Error/
 */
(function($) {
	var defaults = {
		error_url: '/log?type=error',
		info_url: '/log?type=info',
		log_url: '/log?type=log',
		log_level: 1,
		native_error:false,
		use_console:false,
		query_var: 'msg',
		client_info: {
			location:false,
			screen_size:false,
			user_agent:false,
			window_size:false
		}
	};

   /**
	* Initializing with custom options. Not strictly necessary, but recommended.
	* @param  options The custom options.
	*/
	$.clientSideLogging = function(options) {
		$.extend(defaults, options || {});
	};

   /**
    * The function that will send error logs to the server. Also logs to the console using console.error() (if available and requested by the user)
    * @param what What you want to be logged (String, or JSON object)
    */
	$.error = function(what) {
		if (defaults.log_level >= 1) {
			_send(defaults.error_url, what);
		}

		if(window.console&&window.console.error&&defaults.use_console)console.error(what);
	};

   /**
    * The function that will send info logs to the server. Also logs to the console using console.info() (if available and requested by the user)
    * @param what What you want to be logged (String, or JSON object)
    */
	$.info = function(what) {
		if (defaults.log_level >= 3) {
			_send(defaults.info_url, what);
		}

		if(window.console&&window.console.info&&defaults.use_console)console.info(what);
	};

   /**
    * The function that will send standard logs to the server. Also logs to the console using console.log() (if available and requested by the user)
    * @param what What you want to be logged (String, or JSON object)
    */
	$.log = function(what) {
		if (defaults.log_level >= 2) {
			_send(defaults.log_url, what);
		}

		if(window.console&&window.console.log&&defaults.use_console)console.log(what);
	};

   // Log errors whenever there's a generic js error on the page.
	window.onerror = function(message, file, line) {
		if (defaults.native_error) {
			_send(defaults.error_url, {
				message: message,
				file: file,
				line: line
			});
		}
	};

	/*===== Private Functions =====*/
   /**
    * Send the log information to the server.
    * @param url The url to submit the information to.
    * @param what The information to be logged.
    */
	_send = function(url, what) {
		// If the url already has a ? in it.
		if (url.match(/\?.+$/)) {
			url += '&';
		} else {
			url += '?';
		}

		format = 'text';
		if (typeof what === 'object') {
			format = 'json';

			// Let's grab the additional logging info before we send this off.
			$.extend(what, _buildClientInfo());
			what = JSON.stringify(what);
		}

		url += 'format=' + format + '&' + defaults.query_var + '=' + what;
		$.post(url);
	};

   /**
    * Build up an object containing the requested information about the client (as specified in defaults).
    * @return _info The object containing the requested information.
    */
	_buildClientInfo = function() {
		var _info = {};

		if (defaults.client_info.user_agent) {
			_info.user_agent = navigator.userAgent;
		}
		if (defaults.client_info.window_size) {
			_info.window_size = $(window).width()+' x '+$(window).height();
		}
		if (defaults.client_info.screen_size) {
			_info.screen_size = window.screen.availWidth+' x '+window.screen.availHeight;
		}
		if (defaults.client_info.location) {
			_info.location = window.location.href;
		}

		return _info;
	};

   /**
    * Fallback for older browsers that don't implement JSON.stringify
    * @param obj The JSON object to turn into a string.
    * @return A string representation of the JSON object.
    */
	JSON.stringify = JSON.stringify || function (obj) {
		var t = typeof (obj);
		if (t != "object" || obj === null) {
			// simple data type
			if (t == "string") obj = '"'+obj+'"';
			return String(obj);
		} else {
			// recurse array or object
			var n, v, json = [], arr = (obj && obj.constructor == Array);
			for (n in obj) {
				v = obj[n]; t = typeof(v);
				if (t == "string") v = '"'+v+'"';
				else if (t == "object" && v !== null) v = JSON.stringify(v);
				json.push((arr ? "" : '"' + n + '":') + String(v));
			}
			return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
		}
	};
})(jQuery);