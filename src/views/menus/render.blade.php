@foreach ($menu->menuItems as $menuItem)
	<li{{ ($menuItem->model->link() == Request::url()) ? ' class="active"' : '' }}>
		<a href="{{ $menuItem->model->link() }}" class="{{ $menuItem->fmodel == 'Modal' ? 'fancybox' : '' }}">
			{{ $menuItem->model->name }}
			@if ($menuItem->fmodel == 'Modal')
				{{ $menuItem->model->render(); }}
			@endif
		</a>
		@if ($menuItem->childMenu)
			<ul class="nav nav-child">
				{{ View::make('core::menus.render', array('menu'=>$menuItem->childMenu)) }}
			</ul>
		@endif
	</li>
@endforeach