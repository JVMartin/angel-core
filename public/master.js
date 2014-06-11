$(document).ready(function() {
	$('.deleteForm').submit(function(e) {
		var message = $(this).data('confirm');
		if (!confirm(message)) e.preventDefault();
	});

	$('.noSubmitOnEnter').bind('keyup keypress', function(e) {
		var code = e.keyCode || e.which;
		if (code  == 13) {
			e.preventDefault();
			return false;
		}
	});

	$('.expandBelow').click(function() {
		var $icon = $(this).children('.glyphicon');
		if ($icon.hasClass('glyphicon-chevron-down')) {
			$icon.removeClass('glyphicon-chevron-down');
			$icon.addClass('glyphicon-chevron-up');
			$(this).next().stop().slideDown();
		} else {
			$icon.removeClass('glyphicon-chevron-up');
			$icon.addClass('glyphicon-chevron-down');
			$(this).next().stop().slideUp();
		}
	});

	// KCFinder browsing
	$('.imageBrowse').click(function() {
		var $input = $(this).parent().prev();
		window.KCFinder = {};
		window.KCFinder.callBack = function(url) {
			var base_url = config.base_url;
			base_url = base_url.substring(0, base_url.length - 1); // Remove trailing slash
			$input.val(base_url + url);
			window.KCFinder = null;
		};
		window.open('/packages/angel/core/js/kcfinder/browse.php?type=images', 'image_finder', 'width=1000,height=600');
	});

	if ($.isFunction($.fn.datetimepicker)) {
		// Date / time picker
		$('.date-time').datetimepicker({
			format: 'Y-m-d H:i:00'
		});
	}
});