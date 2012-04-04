/*
 *	Title: jQuery Client Side Logging Plugin
 *	Author: Rémy Bach
 *	Version: 0.0.1
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
		query_var: 'msg',
		client_info: {
			user_agent:true,
			window_size:true,
			screen_size:true,
			location:true
		}
	};

	$.fn.clientSideLogging = function(options) {
		$.extend(defaults, options || {});
	};

	$.fn.error = function(what) {
		_send(defaults.error_url, what);
		if(window.console&&window.console.error)console.error.apply(this, arguments);
	};

	$.fn.info = function(what) {
		_send(defaults.info_url, what);
		if(window.console&&window.console.info)console.info.apply(this, arguments);
	};

	$.fn.log = function(what) {
		_send(defaults.log_url, what);
		if(window.console&&window.console.log)console.log.apply(this, arguments);
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

			$.extend(what, _buildClientInfo);
			what = JSON.stringify(what);
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
			_info.window_size = $(window).outerWidth()+' x '+$(window).outerHeight();
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