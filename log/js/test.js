jQuery(function($) {
	$('.no-js').removeClass('no-js');
	$('iframe').height($(document).height());

	var location = false;
	var screen_size = false;
	var user_agent = false;
	var window_size = false;

	$('input:button').click(function(e) {
		e.preventDefault();
		updateLogging();

		if (!location && !screen_size && !user_agent && !window_size) {
			if ($(this).hasClass('error')) {
				$.error($(this).parents('li').find('input:text').val());
			} else if ($(this).hasClass('info')) {
				$.info($(this).parents('li').find('input:text').val());
			} else if ($(this).hasClass('log')) {
				$.log($(this).parents('li').find('input:text').val());
			}
		} else {
			if ($(this).hasClass('error')) {
				$.error({msg:$(this).parents('li').find('input:text').val()});
			} else if ($(this).hasClass('info')) {
				$.info({msg:$(this).parents('li').find('input:text').val()});
			} else if ($(this).hasClass('log')) {
				$.log({msg:$(this).parents('li').find('input:text').val()});
			}
		}

		// Give the ajax request a chance to log this before refreshing the iframe.
		setTimeout(function() {
			// Refresh the iframe.
			$('iframe').attr('src', $('iframe').attr('src'));
		}, 500);
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

	function updateLogging() {
		$.clientSideLogging({
			log_level: 3,
			client_info: {
				location:location,
				screen_size:screen_size,
				user_agent:user_agent,
				window_size:window_size
			}
		});
	}
});