// Drag helper to set width for table cells
function fixHelper(e, ui) {
	ui.children().each(function() {
		$(this).width($(this).width());
	});
	return ui;
}

// For usage in sortable() table bodies
var sortObj = {
	helper: fixHelper,
	handle: '.handle',
	cancel: '',
	stop: function(e, ui) {
		// Update the orders
		var i = 0;
		var send = false;
		var selector = ui.item.data('selector');
		if (!selector) selector = '.orderInput';

		ui.item.parent().children('tr').each(function() {
			var $input = $(this).find(selector);
			if ($input.val() != i) {
				$input.val(i);
				send = true;
			}
			i++;
		});

		// Don't send if nothing's changed.
		if (!send) return;

		// Build our orders
		var orders = {};
		$(this).find(selector).each(function() {
			var id = $(this).closest('tr').data('id');
			orders[id] = $(this).val();
		});

		var url = ui.item.data('url');

		if (url) {
			// Send them off
			$.post(config.admin_url + url, {orders:orders}, function(data) {
				if (data != 1) {
					alert('There was an error connecting to our servers.');
					console.log(data);
					return;
				}
			}).fail(function() {
				alert('There was an error connecting to our servers.');
			});
		}
	}
}

$(function() {
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

	if ($.isFunction($.fn.datetimepicker)) {
		// Date / time picker
		$('.date-time').datetimepicker({
			format: 'Y-m-d H:i:00'
		});
	}
});

function bindImageBrowsers() {
	// KCFinder browsing
	$('.imageBrowse').click(function() {
		var $self = $(this);
		var $input = $self.parent().prev();
		window.KCFinder = {};
		window.KCFinder.callBack = function(url) {
			if ($self.hasClass('imageBrowseAbsolute')) {
				var base_url = config.base_url;
				base_url = base_url.substring(0, base_url.length - 1); // Remove trailing slash
				$input.val(base_url + url); // Absolute URL
			} else {
				$input.val(url); // Relative URL
			}
			window.KCFinder = null;
		};
		window.open('/packages/angel/core/js/kcfinder/browse.php?type=images', 'image_finder', 'width=1000,height=600');
	});
}
$(function() {bindImageBrowsers();});