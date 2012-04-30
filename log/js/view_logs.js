jQuery(function($) {
	$('.navbar input:text').keyup(function(e) {
		var filter = new RegExp(this.value, 'gim');

		$('tr').each(function(i, val) {
			if ($(this).html().match(filter)) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
	});
});