@extends('core::admin.template')

@section('title', 'Menus')

@section('css')
	<style>
		.handle {
			cursor:ns-resize;
		}
		.addLinkWrap {
			display:none;
		}
		.pageDropWrap {
			margin:0 10px;
			width:250px;
			display:inline-block;
		}
		h3 {
			margin:0;
		}
	</style>
@stop

@section('js')
	{{ HTML::script('packages/angel/core/js/jquery/jquery-ui.min.js') }}
	<script>
		var linkable_models = {{ json_encode($linkable_models) }};
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

				var select = linkable_models[model].select;

				if (!select) {
					$('#existingModel').find('.modelType').prop('disabled', true);
					$('#newModel').find('.modelType').click();
					return;
				}

				$('#existingModel').find('.modelType').prop('disabled', false);
				$('#existingModel').find('.modelType').click();
				$("#existingModelWrap").html(select);
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
	</script>
@stop

@section('content')
	<div class="row pad">
		<div class="col-sm-8 pad">
			<h1>Menus</h1>
			<a class="btn btn-sm btn-primary" href="{{ admin_url('menus/add') }}">
				<span class="glyphicon glyphicon-plus"></span>
				Add
			</a>
		</div>
	</div>
	<div class="row text-center">
		{{ $links }}
	</div>
	<div class="row">
		<div class="col-xs-12">
			@foreach($menus as $menu)
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3>
							<a href="{{ admin_url('menus/edit/' . $menu->id) }}" class="btn btn-xs btn-default">
								<span class="glyphicon glyphicon-edit"></span>
							</a>
							{{ $menu->name }}
						</h3>
					</div>
					<div class="panel-body" data-id="{{ $menu->id }}">
						@include('core::admin.menus.items.index')
						<button class="btn btn-sm btn-primary showWizard" data-toggle="modal" data-target="#wizard">
							<span class="glyphicon glyphicon-plus"></span>
							Add Link
						</button>
					</div>
				</div>
			@endforeach
		</div>
	</div>
	<div class="row text-center">
		{{ $links }}
	</div>

	{{-- Wizard --}}
	<div class="modal fade" id="wizard" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Add Link Wizard</h4>
				</div>
				<div class="modal-body">
					{{ Form::open(array('url' => admin_uri('menus/items/add'), 'role'=>'form')) }}
						<div class="wizSlide">
							<p>Where would you like your link to point to?</p>
							<div class="form-group">
								{{ $model_select }}
							</div>
							<p class="text-right">
								<button type="button" class="btn btn-info wizNext">Next</button>
							</p>
						</div>
						<div class="wizSlide">
							<p>What kind of <span class="model"></span> would you like your link to point to?</p>
							<div class="radio" id="existingModel">
								<label>
									{{ Form::radio('modelType', 'existing', true, array('class'=>'modelType')) }}
									Existing <span class="model"></span>
								</label>
							</div>
							<div class="radio" id="newModel">
								<label>
									{{ Form::radio('modelType', 'new', null, array('class'=>'modelType')) }}
									Create New <span class="model"></span>
								</label>
							</div>
							<p class="text-right">
								<button type="button" class="btn btn-info wizNext" id="existingNext">Next</button>
								<a href="{{-- Filled by the javascript --}}" class="btn btn-info" id="newNext">Next</a>
							</p>
						</div>
						<div class="wizSlide">
							<p>Which existing <span class="model"></span> would you like your link to point to?</p>
							<div id="existingModelWrap" class="form-group"></div>
							{{ Form::hidden('menu_id') }}
							<p class="text-right">
								<button type="submit" class="btn btn-success">Add Link</button>
							</p>
						</div>
					{{ Form::close() }}
				</div>{{-- Modal --}}
			</div>{{-- Modal --}}
		</div>{{-- Modal --}}
	</div>{{-- Modal --}}
@stop