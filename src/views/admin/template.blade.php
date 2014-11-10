<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Admin | @yield('title')</title>

	@if ($settings['theme']['value'] == 'default')
		{{ HTML::style('packages/angel/core/bootstrap/bootstrap.min.css') }}
	@elseif ($settings['theme']['value'] == 'slate')
		{{ HTML::style('packages/angel/core/bootstrap/bootstrap-slate.min.css') }}
		{{ HTML::style('packages/angel/core/bootstrap/bootstrap-glyphicons.css') }}
	@elseif ($settings['theme']['value'] == 'darkly')
		{{ HTML::style('packages/angel/core/bootstrap/bootstrap-darkly.min.css') }}
		{{ HTML::style('packages/angel/core/bootstrap/bootstrap-glyphicons.css') }}
	@endif
	{{ HTML::style('packages/angel/core/master.css') }}
	@yield('css')
</head>
<body class="theme-{{ $settings['theme']['value'] }}">
<div id="adminMasterContainer" class="container">
	@include('core::admin.header')
	@include('core::admin.alerts')
	@yield('content')
</div><!-- #adminMasterContainer -->
{{ HTML::script('packages/angel/core/js/jquery/jquery.min.js') }}
{{ HTML::script('packages/angel/core/bootstrap/bootstrap.min.js') }}
<script>
	var config = {
		base_url: '{{ url('/') }}/',
		admin_url: '{{ admin_url('/') }}/'
	};
</script>
{{ HTML::script('packages/angel/core/master.js') }}
@yield('js')
</body>
</html>