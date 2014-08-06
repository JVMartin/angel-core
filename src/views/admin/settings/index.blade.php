@extends('core::admin.template')

@section('title', 'Settings')

@section('js')
	<script>
		$(function() {
			$('#stripe').click(function() {
				if ($(this).hasClass('btn-default')) {
					$(this).removeClass('btn-default').addClass('btn-success').html('Live').prev().val('live');
				} else {
					$(this).removeClass('btn-success').addClass('btn-default').html('Test').prev().val('test');
				}
			});
		});
	</script>
@stop

@section('content')
	<h1>Settings</h1>
	{{ Form::open(array('role'=>'form')) }}
		<div class="row">
			<div class="col-md-9">
				<table class="table table-striped">
					<tbody>
						<tr>
							<td>
								{{ Form::label('title', 'Title') }}
							</td>
							<td>
								<div style="width:300px">
									{{ Form::text('title', $settings['title']['value'], array('class'=>'form-control', 'placeholder'=>'Title')) }}
								</div>
							</td>
						</tr>
						<tr>
							<td>
								{{ Form::label('theme', 'Theme') }}
							</td>
							<td>
								<div style="width:300px">
									{{ Form::select('theme', $settings['theme']['arr'], $settings['theme']['value'], array('class'=>'form-control')) }}
								</div>
							</td>
						</tr>
						@if (Config::get('products::stripe'))
							<tr>
								<td>
									<b>Stripe Mode</b>
								</td>
								<td>
									<input type="hidden" name="stripe" value="{{ $settings['stripe']['value'] }}" />
									@if ($settings['stripe']['value'] == 'test')
										<button type="button" id="stripe" class="btn btn-default">
											Test
										</button>
									@else
										<button type="button" id="stripe" class="btn btn-success">
											Live
										</button>
									@endif
								</td>
							</tr>
						@endif
					</tbody>
				</table>
			</div>{{-- Left Column --}}
		</div>{{-- Row --}}
		<div class="text-right pad">
			<input type="submit" class="btn btn-primary" value="Save" />
		</div>
	{{ Form::close() }}
@stop
