<div class="row">
	<div class="navbar navbar-default">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="{{ url('/') }}">{{ $settings['title']['value'] }}</a>
		</div>
		<div class="navbar-collapse collapse">
			<ul id="mainMenu" class="nav nav-main navbar-nav navbar-right">
				{{ $Menu::find(1)->display() }}
			</ul>
		</div>
	</div>
</div><!-- Header Row -->