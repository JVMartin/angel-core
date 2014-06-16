@extends('core::admin.template')

@section('title', 'Edit Menu Item')

@section('content')
	<h1>Edit Menu Item</h1>
	<br />
	<div class="row">
		<div class="col-sm-10">
			{{ Form::model($item) }}
				<table class="table table-striped">
					<tbody>
						<tr>
							<td>
								{{ Form::label('child_menu_id', 'Child Menu') }}
							</td>
							<td>
								<div style="width:300px">
									{{ Form::select('child_menu_id', $menu_list, null, array('class' => 'form-control', 'autocomplete'=>'off')) }}
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