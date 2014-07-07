@extends('core::template')

@section('title', $page->title)

@section('meta')
	{{ $page->meta_html() }}
@stop

@section('css')
	@if ($page->css)
		<style>
			{{ $page->css }}
		</style>
	@endif
@stop

@section('js')
	@if ($page->js)
		<script>
			{{ $page->js }}
		</script>
	@endif
@stop

@section('content')
	{{ $page->html }}
@stop