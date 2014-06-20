@foreach ($models as $model)
	<?php
		$active = ($model->link() == Request::url()) ? ' class="active"' : '';
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
		<a href="{{ $model->link() }}" class="{{ get_class($model) == 'Modal' ? 'fancybox' : '' }}">
			{{ $model->name() }}
			@if (get_class($model) == 'Modal')
				{{ $model->render(); }}
			@endif
		</a>
		@if ($model->menu_children)
			<ul class="nav nav-child">
				{{ View::make('core::menus.render', array('models'=>$model->menu_children)) }}
			</ul>
		@endif
	</li>
@endforeach