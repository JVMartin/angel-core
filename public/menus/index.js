$(function() {
	$(".linksTable tbody").sortable(sortObj);

	//-----------------
	// Add link wizard
	//-----------------
	$(".showWizard").click(function() {
		$('input[name="menu_id"]').val($(this).closest('.panel-body').data('id'));
	});
	$("#wizard").on('show.bs.modal', function () {
		$(".wizSlide").hide();
		$(".wizSlide").first().show();
		$('.modelType').first().prop('checked', true).trigger('change');
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
			$('#newNext').attr('href', linkable_models[model].add+'?menu_id='+$('input[name="menu_id"]').val());
		}
	});

	//-----------------
	// Delete link
	//-----------------
	$(".deleteLink").click(function() {
		if (!confirm("Delete this link?")) return;
		var $tr = $(this).closest('tr');
		$.post(config.admin_url+"menus/items/delete/"+$tr.data('id')+"/1", {}, function(data) {
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