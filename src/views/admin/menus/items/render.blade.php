<table class="table table-striped linksTable">
	<thead>
		<tr>
			<th></th>
			<th style="width:100px">Type</th>
			@if (Config::get('core::languages') && !$single_language)
				<th style="width:120px">Language</th>
			@endif
			<th>Edit</th>
		</tr>
	</thead>
	<tbody data-url="menus/items/order">
		<?php
		$models = Menu::get_models($menu->menuItems);
		?>
		@foreach ($menu->menuItems as $menu_item)
			<tr data-id="{{ $menu_item->id }}">
				<td style="width:190px;">
					<button type="button" class="btn btn-xs btn-default handle">
						<span class="glyphicon glyphicon-resize-vertical"></span>
					</button>
					@if (get_class($models[$menu_item->order]) != 'Modal')
						<a href="{{ $models[$menu_item->order]->link() }}" class="btn btn-xs btn-info" target="_blank">
							<span class="glyphicon glyphicon-eye-open"></span>
						</a>
					@endif
					<a href="{{ admin_url('menus/items/edit/' . $menu_item->id) }}" class="btn btn-xs btn-default">
						<span class="glyphicon glyphicon-edit"></span>
					</a>
					<button type="button" class="btn btn-xs btn-danger deleteLink">
						<span class="glyphicon glyphicon-remove"></span>
					</button>
				</td>
				<td>
					{{ get_class($models[$menu_item->order]) }}
				</td>
				@if (Config::get('core::languages') && !$single_language)
					<td>
						{{ $models[$menu_item->order]->language->name }}
					</td>
				@endif
				<td>
					{{ Form::hidden(null, $menu_item->order, array('class'=>'orderInput')) }}
					<a href="{{ $models[$menu_item->order]->link_edit() }}">
						{{ $models[$menu_item->order]->name() }}
					</a>
				</td>
			</tr>
		@endforeach
	</tbody>
</table>