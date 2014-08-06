@extends('core::admin.template')

@section('title', ucfirst($action).' Language')

@section('content')
	<h1>{{ ucfirst($action) }} Language</h1>
	@if ($action == 'edit')
		{{ Form::open(array('role'=>'form',
							'url'=>admin_uri('languages/hard-delete/'.$language->id),
							'class'=>'deleteForm',
							'data-confirm'=>"WARNING:\nYou are about to delete an entire language and all related content irretrievably.\nAre you sure you want to do this?")) }}
			<input type="submit" class="btn btn-sm btn-danger" value="Delete Forever" />
		{{ Form::close() }}
	@endif
	<div class="row">
		<div class="col-sm-10">
			@if ($action == 'edit')
				{{ Form::model($language, array('role'=>'form')) }}
			@elseif ($action == 'add')
				{{ Form::open(array('role'=>'form')) }}
			@endif
				<table class="table table-striped">
					<tbody>
						<tr>
							<td>
								{{ Form::label('name', 'Name') }}
							</td>
							<td>
								<div style="width:300px">
									{{ Form::text('name', null, array('class'=>'form-control', 'placeholder'=>'Name')) }}
								</div>
							</td>
						</tr>
						<tr>
							<td>
								{{ Form::label('uri', 'URI') }}
							</td>
							<td>
								<div style="width:300px">
									{{ Form::text('uri', null, array('class'=>'form-control', 'placeholder'=>'URI')) }}
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="text-right pad">
					<input type="submit" class="btn btn-primary" value="Save" />
				</div>
			{{ Form::close() }}
		</div>
	</div>
@stop