<div class="row">
	<div class="navbar navbar-inverse">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="{{ url('/') }}">{{ $settings['title']['value'] }}</a>
		</div>
		<div class="navbar-collapse collapse">
			<ul class="nav nav-main navbar-nav navbar-right">
				{{ $menuModel::display(1) }}
			</ul>
		</div>
	</div>
</div><!-- Header Row -->