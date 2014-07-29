@extends('core::admin.template')

@section('title', 'Settings')

@section('content')
	<h1>Settings</h1>
	{{ Form::open(array('role'=>'form', 'method'=>'post')) }}
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
								<td></td>
								<td>
									<label>
										{{ Form::checkbox('stripe', $settings['stripe']['value']) }} Stripe Production Mode
									</label>
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
