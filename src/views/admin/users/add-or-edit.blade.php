@extends('core::admin.template')

@section('title', ucfirst($action) . ' User')

@section('content')
<div class="row">
	<div class="col-md-7 col-sm-8">
		<h1>{{ ucfirst($action) }} User</h1>
		@if ($action == 'edit' && $edit_user != Auth::user())
			@if (!$edit_user->deleted_at)
				{{ Form::open(array('role'=>'form',
									'url'=>admin_uri('users/delete/'.$edit_user->id),
									'style'=>'margin-bottom:15px;')) }}
					<input type="submit" class="btn btn-sm btn-danger" value="Delete" />
				{{ Form::close() }}
			@else
				{{ Form::open(array('role'=>'form',
									'url'=>admin_uri('users/hard-delete/'.$edit_user->id),
									'class'=>'deleteForm',
									'data-confirm'=>'Delete this user forever?')) }}
					<input type="submit" class="btn btn-sm btn-danger" value="Delete Forever" />
				{{ Form::close() }}
				<a href="{{ admin_url('users/restore/'.$edit_user->id) }}" class="btn btn-sm btn-success">Restore</a>
			@endif
		@endif
		@if ($action == 'edit')
			{{ Form::model($edit_user, array('role'=>'form')) }}
		@elseif ($action == 'add')
			{{ Form::open(array('role'=>'form')) }}
		@endif
			<table class="table table-striped">
				<tbody>
					<tr>
						<td>
							<span class="required">*</span>
							{{ Form::label('type', 'Type') }}
						</td>
						<td>
							{{ Form::select('type', $okay_types, null, array('class' => 'form-control', 'required')) }}
						</td>
					</tr>
					<tr>
						<td>
							<span class="required">*</span>
							{{ Form::label('email', 'Email') }}
						</td>
						<td>
							{{ Form::text('email', null, array('class'=>'form-control', 'placeholder'=>'Email', 'required')) }}
						</td>
					</tr>
					<tr>
						<td>
							<span class="required">*</span>
							{{ Form::label('username', 'Username') }}
							<br />(4 - 16 characters)
						</td>
						<td>
							{{ Form::text('username', null, array('class'=>'form-control', 'placeholder'=>'Username', 'required')) }}
						</td>
					</tr>
					<tr>
						<td>
							{{ Form::label('first_name', 'First Name') }}
						</td>
						<td>
							{{ Form::text('first_name', null, array('class'=>'form-control', 'placeholder'=>'First Name')) }}
						</td>
					</tr>
					<tr>
						<td>
							{{ Form::label('last_name', 'Last Name') }}
						</td>
						<td>
							{{ Form::text('last_name', null, array('class'=>'form-control', 'placeholder'=>'Last Name')) }}
						</td>
					</tr>
					@if ($action == 'add')
						<tr>
							<td>
								<span class="required">*</span>
								{{ Form::label('password', 'Password') }}
								<br />(Min. 6 characters)
							</td>
							<td>
								{{ Form::password('password', array('class'=>'form-control', 'placeholder'=>'Password', 'required')) }}
							</td>
						</tr>
						<tr>
							<td>
								<span class="required">*</span>
								{{ Form::label('password_confirmation', 'Confirm Password') }}
							</td>
							<td>
								{{ Form::password('password_confirmation', array('class'=>'form-control', 'placeholder'=>'Confirm Password', 'required')) }}
							</td>
						</tr>
					@endif
				</tbody>
			</table>
			<div class="text-right pad">
				<input type="submit" class="btn btn-primary" value="Save" />
			</div>
		{{ Form::close() }}
		@if ($action == 'edit')
			<h3>Password Reset</h3>
			{{ Form::open(array('role'=>'form', 'url'=>admin_uri('users/password/' . $edit_user->id))) }}
				<table class="table table-striped">
					<tbody>
						<tr>
							<td>
								<span class="required">*</span>
								{{ Form::label('password', 'Password') }}
								<br />(Min. 6 characters)
							</td>
							<td>
								{{ Form::password('password', array('class'=>'form-control', 'placeholder'=>'Password', 'required')) }}
							</td>
						</tr>
						<tr>
							<td>
								<span class="required">*</span>
								{{ Form::label('password_confirmation', 'Confirm Password') }}
							</td>
							<td>
								{{ Form::password('password_confirmation', array('class'=>'form-control', 'placeholder'=>'Confirm Password', 'required')) }}
							</td>
						</tr>
					</tbody>
				</table>
				<div class="text-right pad">
					<input type="submit" class="btn btn-primary" value="Reset Password" />
				</div>
			{{ Form::close() }}
		@endif
	</div>
</div>
@stop