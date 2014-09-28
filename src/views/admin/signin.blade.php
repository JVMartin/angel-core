@extends('core::admin.template')

@section('title', 'Sign In')

@section('content')
	<div class="row">
		<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
			{{ Form::open(array('role'=>'form')) }}
				<h1>Sign In</h1>
				<div class="form-group">
					{{ Form::label('loguser', 'Username or Email', array('class'=>'sr-only')) }}
					{{ Form::text('loguser', null, array('class'=>'form-control', 'placeholder'=>'Username or Email', 'autofocus')) }}
				</div>
				<div class="form-group">
					{{ Form::label('logpass', 'Password', array('class'=>'sr-only')) }}
					{{ Form::password('logpass', array('class'=>'form-control', 'placeholder'=>'Password')) }}
				</div>
				<div class="checkbox">
					<label>
						{{ Form::checkbox('remember') }}
						Remember Me
					</label>
				</div>
				<p class="text-right">
					<input type="submit" class="btn btn-primary" value="Sign In" />
				</p>
			{{ Form::close() }}
		</div>
	</div>
@stop