# jQuery Client Side Logging Plugin

## About

The idea behind this plugin was born from [reading this article](http://openmymind.net/2012/4/4/You-Really-Should-Log-Client-Side-Error/).
The plugin has the ability to include some useful stats like the user agent, window size, and hopefully more in the future.

## Usage

The first thing that needs be done is you need to specify the urls the messages will be sent to (the ones in the example below are the default ones):

	$.clientSideLogging({
		error_url: '/log?type=error',	// The url to which errors logs are sent
		info_url: '/log?type=info',		// The url to which info logs are sent
		log_url: '/log?type=log',		// The url to which standard logs are sent
		log_level: 1,					// The level at which to log. This allows you to keep the calls to the logging in your code and just change this variable to log varying degrees. 1 = only error, 2 = error & log, 3 = error, log & info
		native_error:false,				// Whether or not to send native js errors as well (using window.onerror).
		use_console:true,				// Whether to show a console.error/info/log as well (when a console is present)
		query_var: 'msg',				// The variable to send the log message through as.
		client_info: {					// Configuration for what info about the client's browser is logged.
			location:true,				//	The url to the page on which the error occurred.
			screen_size:true,			//	The size of the user's screen (different to the window size because the window might not be maximized)
			user_agent:true,			//	The user agent string.
			window_size:true			//	The window size.
		}
	});

*Important to note* at this point is that you need to have something set up on the server side to receive the errors and handle them accordingly.
Thanks to [Rob Miller](https://github.com/robmiller) for the example logging functionality and for contributing in general. You can find this in the [test directory](https://github.com/remybach/jQuery.clientSideLogging/tree/master/test)

Once you've specified the urls (or are happy with the defaults), you have three utility/wrapper functions available to you (all of which receive either a string or a json formatted object):

* `$.error(what)` - Send an error message to the server, also does a console.error (if available)
* `$.info(what)` - Send an info message to the server, also does a console.info (if available)
* `$.log(what)` - Send a log message to the server, also does a console.log (if available)

If a string is received, the info will be sent to the backend as a normal post request which might look similar to the following:

	$.post('/log?type=error&msg=YOUR_ERROR_MESSAGE');

Otherwise if a JSON object is received, the info will be sent as an encoded JSON string that can be decoded on the backend.