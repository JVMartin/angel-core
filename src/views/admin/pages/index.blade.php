@extends('core::admin.template')

@section('title', 'Pages')

@section('js')
	@if (Config::get('core::languages'))
		<script>
			$(function() {
				$('#copyChecked').click(function(e) {
					if (!$('.idCheckbox:checked').length) {
						alert('You must first select at least one page to copy!');
						return;
					}
					$('#all').val(0);
					$('#copyModal').modal('show');
				});
				$('#copyAll').click(function() {
					$('#all').val(1);
				});
			});
		</script>
	@endif
@stop

@section('content')
	<div class="row pad">
		<div class="col-sm-8 pad">
			<h1>Pages</h1>
			<a class="btn btn-sm btn-primary" href="{{ admin_url('pages/add') }}">
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
		<div class="col-sm-9">
			<table class="table table-striped">
				<thead>
					<tr>
						<th style="width:80px;"></th>
						<th style="width:80px;">ID</th>
						<th>URL</th>
						<th>Name</th>
						<th>Title</th>
					</tr>
				</thead>
				<tbody>
				@foreach ($pages as $page)
					<tr>
						<td>
							<a href="{{ $page->link_edit() }}" class="btn btn-xs btn-default">
								<span class="glyphicon glyphicon-edit"></span>
							</a>
							<a href="{{ $page->link() }}" class="btn btn-xs btn-info" target="_blank">
								<span class="glyphicon glyphicon-eye-open"></span>
							</a>
						</td>
						<td>{{ $page->id }}</td>
						<td>{{ $page->url }}</td>
						<td>{{ $page->name }}</td>
						<td>{{ $page->title }}</td>
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