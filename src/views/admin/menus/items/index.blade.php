<table class="table table-striped linksTable">
	<thead>
		<tr>
			<th style="width:190px;"></th>
			<th style="width:100px">Type</th>
			<th>Edit</th>
			<th>Child Menu</th>
		</tr>
	</thead>
	<tbody data-url="menus/items/order">
		<?php $menu->fillItems(); ?>
		@foreach ($menu->menuItems as $menuItem)
			<tr data-id="{{ $menuItem->id }}">
				<td style="width:190px;">
					<button type="button" class="btn btn-xs btn-default handle">
						<span class="glyphicon glyphicon-resize-vertical"></span>
					</button>
					@if ($menuItem->fmodel != 'Modal')
						<a href="{{ $menuItem->model->link() }}" class="btn btn-xs btn-info" target="_blank">
							<span class="glyphicon glyphicon-eye-open"></span>
						</a>
					@endif
					<a href="{{ admin_url('menus/items/edit/' . $menuItem->id) }}" class="btn btn-xs btn-default">
						<span class="glyphicon glyphicon-edit"></span>
					</a>
					<button type="button" class="btn btn-xs btn-danger deleteLink">
						<span class="glyphicon glyphicon-remove"></span>
					</button>
				</td>
				<td>
					{{ $menuItem->fmodel }}
				</td>
				<td>
					{{ Form::hidden(null, $menuItem->order, array('class'=>'orderInput')) }}
					<a href="{{ $menuItem->model->link_edit() }}">
						{{ $menuItem->model->name }}
					</a>
				</td>
				<td>
					@if ($menuItem->childMenu)
						{{ $menuItem->childMenu->name }}
					@endif
				</td>
			</tr>
		@endforeach
	</tbody>
</table>