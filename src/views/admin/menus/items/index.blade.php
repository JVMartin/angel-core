<table class="table table-striped linksTable">
	<thead>
		<tr>
			<th style="width:190px;"></th>
			<th style="width:100px">Type</th>
			@if (Config::get('core::languages') && !$single_language)
				<th style="width:120px">Language</th>
			@endif
			<th>Edit</th>
			<th>Child Menu</th>
		</tr>
	</thead>
	<tbody data-url="menus/items/order">
		@foreach ($menu->menuItems as $menu_item)
			<tr data-id="{{ $menu_item->id }}">
				<td style="width:190px;">
					<button type="button" class="btn btn-xs btn-default handle">
						<span class="glyphicon glyphicon-resize-vertical"></span>
					</button>
					@if (get_class($menu_item->linkable) != 'Modal')
						<a href="{{ $menu_item->linkable->link() }}" class="btn btn-xs btn-info" target="_blank">
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
					{{ $menu_item->linkable_type }}
				</td>
				@if (Config::get('core::languages') && !$single_language)
					<td>
						{{ $menu_item->linkable->language->name }}
					</td>
				@endif
				<td>
					{{ Form::hidden(null, $menu_item->order, array('class'=>'orderInput')) }}
					<a href="{{ $menu_item->linkable->link_edit() }}">
						{{ $menu_item->linkable->name() }}
					</a>
				</td>
				<td>
					@if ($menu_item->childMenu)
						{{ $menu_item->childMenu->name }}
					@endif
				</td>
			</tr>
		@endforeach
	</tbody>
</table>