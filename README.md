# jQuery Client Side Logging Plugin

## NOTE

This is in an alpha state. I've had the chance to test it from the front end, and if my console in Chrome is to be believed, then everything's being sent through as expected and the back end should hopefully be receiving things correctly.

If anyone wants to contribute and take part in this, feel free!

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

Once you've specified the urls (or are happy with the defaults), you have three utility/wrapper functions available to you (all of which receive either a string or a json formatted object):

* `$.error(what)` - Send an error message to the server, also does a console.error (if available)
* `$.info(what)` - Send an info message to the server, also does a console.info (if available)
* `$.log(what)` - Send a log message to the server, also does a console.log (if available)

If a string is received, the info will be sent to the backend as a normal post request which might look similar to the following:

	$.post('/log?type=error&msg=YOUR_ERROR_MESSAGE&location=http://location.to/your/page');

Otherwise if a JSON object is received, the info will be sent as an encoded JSON string that can be decoded on the backend.

Lastly, the client information will get sent through named as displayed above.

## TODOs

* Actually get around to testing this properly.
* Add the ability to pass in success and error callbacks.
* Add more choices to the client_info option.