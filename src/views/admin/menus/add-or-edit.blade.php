@extends('core::admin.template')

@section('title', ucfirst($action).' Menu')

@section('content')
	<h1>{{ ucfirst($action) }} Menu</h1>
	@if ($action == 'edit')
		@if (!$menu->deleted_at)
			{{ Form::open(array('role'=>'form',
								'url'=>admin_uri('menus/delete/'.$menu->id),
								'style'=>'margin-bottom:15px;')) }}
				<input type="submit" class="btn btn-sm btn-danger" value="Delete" />
			{{ Form::close() }}
		@else
			{{ Form::open(array('role'=>'form',
								'url'=>admin_uri('menus/hard-delete/'.$menu->id),
								'class'=>'deleteForm',
								'data-confirm'=>'Delete this menu forever?')) }}
				<input type="submit" class="btn btn-sm btn-danger" value="Delete Forever" />
			{{ Form::close() }}
			<a href="{{ admin_url('menus/restore/'.$menu->id) }}" class="btn btn-sm btn-success">Restore</a>
		@endif
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
						@if (Config::get('core::languages'))
							<tr>
								<td>
									<span class="required">*</span>
									{{ Form::label('language_id', 'Language') }}
								</td>
								<td>
									<div style="width:300px">
										{{ Form::select('language_id', $language_drop, $active_language->id, array('class' => 'form-control', 'required')) }}
									</div>
								</td>
							</tr>
						@endif
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