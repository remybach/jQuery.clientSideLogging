/*
 *	Title: jQuery Client Side Logging Plugin
 *	Author: Rémy Bach
 *	Version: 0.0.2
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
		use_console:true,
		query_var: 'msg',
		client_info: {
			location:false,
			screen_size:false,
			user_agent:false,
			window_size:false
		}
	};

	$.clientSideLogging = function(options) {
		$.extend(defaults, options || {});
	};

	$.error = function(what) {
		if (defaults.log_level >= 1) {
			_send(defaults.error_url, what);
		}

		if(window.console&&window.console.error&&defaults.use_console)console.error(what);
	};

	$.info = function(what) {
		if (defaults.log_level >= 3) {
			_send(defaults.info_url, what);
		}

		if(window.console&&window.console.info&&defaults.use_console)console.info(what);
	};

	$.log = function(what) {
		if (defaults.log_level >= 2) {
			_send(defaults.log_url, what);
		}

		if(window.console&&window.console.log&&defaults.use_console)console.log(what);
	};

	/*===== Private Functions =====*/
	_send = function(url, what) {
		if (url.match(/\?.+$/)) {
			url += '&';
		} else {
			url += '?';
		}

		format = 'text';
		if (typeof what === 'object') {
			// Let's grab the additional logging info before we send this off.
			format = 'json';

			$.extend(what, _buildClientInfo());
			what = JSON.stringify(what);
		} else {
			// Add the client info to what was passed through.
			$.each(_buildClientInfo(), function(name, val) {
				what += '&'+name+'='+val;
			});
		}

		url += 'format=' + format + '&' + defaults.query_var + '=' + what;
		$.post(url);
	};

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

	// Fallback for older browsers that don't implement JSON.stringify
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