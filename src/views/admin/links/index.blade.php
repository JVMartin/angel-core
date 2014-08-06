@extends('core::admin.template')

@section('title', 'Links')

@section('content')
	<div class="row pad">
		<div class="col-sm-8 pad">
			<h1>Links</h1>
			<a class="btn btn-sm btn-primary" href="{{ admin_url('links/add') }}">
				<span class="glyphicon glyphicon-plus"></span>
				Add
			</a>
		</div>
		<div class="col-sm-4 well">
			{{ Form::open(array('role'=>'form', 'method'=>'get')) }}
				<div class="form-group">
					<label>Search</label>
					<input type="text" name="search" class="form-control" value="{{ $search }}" />
				</div>
				<div class="text-right">
					<input type="submit" class="btn btn-primary" value="Search" />
				</div>
			{{ Form::close() }}
		</div>
	</div>
	<div class="row text-center">
		{{ $links }}
	</div>
	<div class="row">
		<div class="col-sm-8">
			<table class="table table-striped">
				<thead>
					<tr>
						<th style="width:80px;"></th>
						<th style="width:80px;">ID</th>
						<th>Name</th>
						<th>URL</th>
					</tr>
				</thead>
				<tbody>
				@foreach($link_models as $link)
					<tr>
						<td>
							<a href="{{ $link->link_edit() }}" class="btn btn-xs btn-default">
								<span class="glyphicon glyphicon-edit"></span>
							</a>
						</td>
						<td>{{ $link->id }}</td>
						<td>{{ $link->name }}</td>
						<td><a href="{{ $link->link() }}" target="_blank">{{ $link->link() }}</a></td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	</div>
	<div class="row text-center">
		{{ $links }}
	</div>
@stop