@foreach ($menu->menuItems as $menuItem)
	<?php
		$active = ($menuItem->model->link() == Request::url()) ? ' class="active"' : '';
		$unique = uniqid();
	?>
	
	@if ($active)
		@section('js')
			<script>
				$(function(){
					$('#menuItem{{ $unique }}').parent('ul').closest('li').addClass('active');
				});
			</script>
		@stop
	@endif

	<li{{ $active }} id="menuItem{{ $unique }}">
		<a href="{{ $menuItem->model->link() }}" class="{{ $menuItem->fmodel == 'Modal' ? 'fancybox' : '' }}">
			{{ $menuItem->model->name() }}
			@if ($menuItem->fmodel == 'Modal')
				{{ $menuItem->model->render(); }}
			@endif
		</a>
		@if (!isset($noDeeper) && $menuItem->childMenu)
			<ul class="nav nav-child">
				{{ View::make('core::menus.render', array('menu'=>$menuItem->childMenu, 'noDeeper'=>true)) }}
			</ul>
		@endif
	</li>
@endforeach