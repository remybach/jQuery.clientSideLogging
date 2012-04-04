# jQuery Client Side Logging Plugin

## NOTE

This is in a completely alpha state - I've not had the chance to do any testing yet. If anyone wants to contribute and take part in this, feel free!

## About

The idea behind this plugin was born from (reading this article)[http://openmymind.net/2012/4/4/You-Really-Should-Log-Client-Side-Error/].
The plugin has the ability to include some useful stats like the user agent, window size, and hopefully more in the future.

## Usage

The first thing that needs be done is you need to specify the urls the messages will be sent to (the ones in the example below are the default ones):

	$.clientSideLogging({
		error_url: '/error',
		info_url: '/info',
		log_url: '/log',
		query_var: 'msg',
		client_info: {
			user_agent:true,
			window_size:true
		}
	});

*Important to note* at this point is that you need to have something set up on the server side to receive the errors and handle them accordingly.

Once you've specified the urls (or are happy with the defaults), you have three utility/wrapper functions available to you (all of which receive either a string or a json formatted object):

* `$.error(what)` - Send an error message to the server, also does a console.error (if available)
* `$.info(what)` - Send an info message to the server, also does a console.info (if available)
* `$.log(what)` - Send a log message to the server, also does a console.log (if available)

If a string is received, the `query_var` option is what will be passed to the url... so using the default would make the following post request:

	`$.post('/error?msg=YOUR_ERROR_MESSAGE');`

If it's a json formatted object, this will get passed as a data object as follows:

	`$.post('/error', { your:custom, information:here });`

## TODOs

* Actually get around to testing this properly.
* Add the ability to pass in success and error callbacks.
* Make the _buildClientInfo function accommodate strings too.
* Add more options to the client_info option.