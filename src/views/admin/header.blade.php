<div class="row">
	<div class="navbar navbar-inverse">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="{{ admin_url() }}">Admin</a>
		</div>
		@if (Auth::check() && Auth::user()->is_admin())
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-right">
					@foreach (Config::get('core::menu') as $name=>$uri)
						<li{{ (Request::is(admin_uri($uri) . '*')) ? ' class="active"' : '' }}>
							<a href="{{ admin_url($uri) }}">
								{{ $name }}
							</a>
						</li>
					@endforeach
					<li>
						<a href="{{ url('signout') }}">
							Sign Out
						</a>
					</li>
				</ul>
			</div>
		@endif
	</div>
</div><!-- Header Row -->