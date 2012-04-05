jQuery(function($) {
	$('.no-js').removeClass('no-js');

	var location = false;
	var screen_size = false;
	var user_agent = false;
	var window_size = false;

	$('input:button').click(function(e) {
		e.preventDefault();
		initLogging();

		if ($(this).hasClass('error')) {
			$.error($(this).parents('li').find('input:text').val());
		} else if ($(this).hasClass('info')) {
			$.info($(this).parents('li').find('input:text').val());
		} else if ($(this).hasClass('log')) {
			$.log($(this).parents('li').find('input:text').val());
		}

		location.href = '';
	});

	$('input:checkbox').click(function(e) {
		// toggle our variables
		if (this.name == 'location') {
			location = !location;
		} else if (this.name == 'screen_size') {
			screen_size = !screen_size;
		} else if (this.name == 'user_agent') {
			user_agent = !user_agent;
		} else if (this.name == 'window_size') {
			window_size = !window_size;
		}
	});

	function initLogging() {
		$.clientSideLogging({
			error_url: 'log.php?type=error',
			info_url: 'log.php?type=info',
			log_url: 'log.php?type=log',
			log_level: 3,
			native_error:true,
			use_console:false,
			client_info: {
				location:location,
				screen_size:screen_size,
				user_agent:user_agent,
				window_size:window_size
			}
		});
	}
});