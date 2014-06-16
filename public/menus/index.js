$(function() {
	$(".linksTable tbody").sortable(sortObj);

	//-----------------
	// Add link wizard
	//-----------------
	var menu_id = '';
	$(".showWizard").click(function() {
		var $td = $(this).closest('td');
		menu_id = $td.data('id');
		$('input[name="menu_id"]').val(menu_id);
	});
	$("#wizard").on('show.bs.modal', function () {
		resetModal();
	});
	$(".wizNext").click(function() {
		var $wizSlide = $(this).closest('.wizSlide');
		$wizSlide.hide().next().show();
	});

	var model = '';
	$("#modelSelect").change(function() {
		model = $(this).val();
		$(".model").html(model);
		$.post(config.admin_url+"menus/model-drop-down", {model:model}, function(data) {
			if (data == '0') {
				$('#existingModel').find('.modelType').prop('disabled', true);
				$('#newModel').find('.modelType').click();
				return;
			}
			$('#existingModel').find('.modelType').prop('disabled', false);
			$('#existingModel').find('.modelType').click();
			$("#existingModelWrap").html(data);
		}).fail(function() {
			alert('There was an error connecting to our servers.');
		});
	});
	$("#modelSelect").trigger('change');
	$('#existingModel').click(function() {
		if (!$(this).find('input:disabled').length) return;
		alert('No ' + $('#modelSelect').val() + ' exists yet!');
	});

	$('.modelType').change(function() {
		var type = $(".modelType:checked").val();
		$('#'+type+'Next').show();
		$('#'+type+'Next').siblings().hide();
		if (type == 'new') {
			$('#newNext').attr('href', linkable_models[model].add+'?menu_id='+menu_id);
		}
	});

	function resetModal() {
		$(".wizSlide").hide();
		$(".wizSlide").first().show();
		$('.modelType').first().prop('checked', true).trigger('change');
	}
	resetModal();

	//-----------------
	// Delete link
	//-----------------
	$(".deleteLink").click(function() {
		if (!confirm("Delete this link?")) return;
		var $tr = $(this).closest('tr');
		$.post(config.admin_url+"menus/item-delete", {id:$tr.data('id')}, function(data) {
			if (data != 1) {
				alert('There was an error connecting to our servers.');
				console.log(data);
				return;
			}
			$tr.remove();
		}).fail(function() {
			alert('There was an error connecting to our servers.');
		});
	});
});