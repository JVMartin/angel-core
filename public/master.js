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
		var $tbody = ui.item.parent('tbody');
		var send = false;
		var selector = $tbody.data('selector');
		if (!selector) selector = '.orderInput';
		var orders = {};

		var i = 0;
		$tbody.children('tr').each(function() {
			var $this = $(this);
			var id = $this.data('id');
			var $input = $this.find(selector);
			orders[id] = i;
			if ($input.val() != i) {
				$input.val(i);
				send = true;
			}
			i++;
		});

		var url = $tbody.data('url');
		// Don't send if nothing's changed or no url is provided.
		if (!send || !url) return;

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
		var $this = $(this);
		var $icon = $this.children('.glyphicon');
		if ($icon.hasClass('glyphicon-chevron-down')) {
			$icon.removeClass('glyphicon-chevron-down');
			$icon.addClass('glyphicon-chevron-up');
			$this.next().stop().slideDown();
		} else {
			$icon.removeClass('glyphicon-chevron-up');
			$icon.addClass('glyphicon-chevron-down');
			$this.next().stop().slideUp();
		}
	});

	if ($.isFunction($.fn.datetimepicker)) {
		// Date / time picker
		$('input.date-time').datetimepicker({
			format: 'Y-m-d H:i:00'
		});
		// Date picker
		$('input.date').datetimepicker({
			timepicker:false,
			format: 'Y-m-d'
		});
	}
});

function numberWithCommas(x) {
	var parts = x.toString().split(".");
	parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	return parts.join(".");
}

function doKCBind($button, type) {
	var $input = $button.parents().has('input').first().find('input');
	window.KCFinder = {};
	window.KCFinder.callBack = function(url) {
		if ($button.hasClass('browseAbsolute')) {
			var base_url = config.base_url;
			base_url = base_url.substring(0, base_url.length - 1); // Remove trailing slash
			$input.val(base_url + url); // Absolute URL
		} else {
			$input.val(url); // Relative URL
		}
		window.KCFinder = null;
	};
	window.open('/packages/angel/core/js/kcfinder/browse.php?type='+type+'s', type+'_finder', 'width=1000,height=600');
}
function bindImageBrowsers() {
	// KCFinder browsing
	$('.imageBrowse').unbind('click').click(function() {
		doKCBind($(this), 'image');
	});
}
$(function() {bindImageBrowsers();});

function bindFileBrowsers() {
	// KCFinder browsing
	$('.fileBrowse').unbind('click').click(function() {
		doKCBind($(this), 'file');
	});
}
$(function() {bindFileBrowsers();});