@extends('core::admin.template')

@section('title', ucfirst($action).' Menu')

@section('content')
	<h1>{{ ucfirst($action) }} Menu</h1>
	@if ($action == 'edit')
		{{ Form::open(array('role'=>'form',
							'url'=>admin_uri('menus/delete/'.$menu->id),
							'class'=>'deleteForm',
							'data-confirm'=>'Delete this menu forever?')) }}
			<input type="submit" class="btn btn-sm btn-danger" value="Delete Forever" />
		{{ Form::close() }}
	@endif
	<div class="row">
		<div class="col-sm-10">
			@if ($action == 'edit')
				{{ Form::model($menu, array('role'=>'form')) }}
			@elseif ($action == 'add')
				{{ Form::open(array('role'=>'form')) }}
			@endif
				<table class="table table-striped">
					<tbody>
						<tr>
							<td>
								<span class="required">*</span>
								{{ Form::label('name', 'Name') }}
							</td>
							<td>
								<div style="width:300px">
									{{ Form::text('name', null, array('class'=>'form-control', 'placeholder'=>'Name', 'required')) }}
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