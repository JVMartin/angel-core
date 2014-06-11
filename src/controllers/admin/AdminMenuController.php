<?php

class AdminMenuController extends AdminCrudController {

	public $model		= 'Menu';
	public $plural		= 'menus';
	public $singular	= 'menu';
	public $package		= 'core';

	public function index()
	{
		$paginator = Menu::withTrashed()->with('menuItems');
		if (Config::get('core::languages')) {
			$paginator = $paginator->where('language_id', $this->data['active_language']->id);
		}
		$paginator = $paginator->paginate(5);

		$model_list = array();
		$this->data['linkable_models'] = array();

		foreach (Config::get('core::linkable_models') as $model=>$uri) {
			$model_list[$model] = $model;
			$this->data['linkable_models'][$model] = array(
				'add' => admin_url($uri.'/add')
			);
		}
		
		$this->data['model_select'] = Form::select('fmodel', $model_list, null, array('id'=>'modelSelect', 'class' => 'form-control', 'autocomplete'=>'off'));

		$menus = Menu::all();
		$menu_list = array('0'=>'None');
		foreach ($menus as $menu) {
			$menu_list[$menu->id] = $menu->name;
		}
		
		$this->data['menus'] = $paginator->getCollection();
		$appends = $_GET;
		unset($appends['page']);
		$this->data['links'] = $paginator->appends($appends)->links();

		return View::make('core::admin.menus.index', $this->data);
	}

	public function validate_rules($id = null)
	{
		return array(
			'name' => 'required'
		);
	}

	public function item_add()
	{
		$order = MenuItem::where('menu_id', Input::get('menu_id'))->count();

		$menu_item = new MenuItem;
		$menu_item->order	= $order;
		$menu_item->menu_id = Input::get('menu_id');
		$menu_item->fmodel	= Input::get('fmodel');
		$menu_item->fid		= Input::get('fid');
		$menu_item->save();

		return Redirect::to(admin_uri('menus'))->with('success', '
			<p>Link created.</p>
		');
	}

	public function reorder($id)
	{
		$menu_items = Menu::findOrFail($id)->menuItems;
		$i = 0;
		foreach($menu_items as $menu_item) {
			$menu_item->order = $i;
			$menu_item->save();
			$i++;
		}
	}

	/**
	 * AJAX for reordering menu items
	 */
	public function item_order()
	{
		$orders = Input::get('orders');
		$menu_items = MenuItem::whereIn('id', array_keys($orders))->get();
		foreach($menu_items as $menu_item) {
			$menu_item->order = $orders[$menu_item->id];
			//echo "Item: " . $menu_item->id . " | Order: " . $orders[$menu_item->id] . "\n";
			$menu_item->save();
		}
		return 1;
	}

	public function item_edit($id)
	{
		$menus = Menu::all();
		$menu_list = array('0'=>'None');
		foreach ($menus as $menu) {
			$menu_list[$menu->id] = $menu->name;
		}

		$this->data['menu_item'] = MenuItem::find($id);
		$this->data['menu_list'] = $menu_list;

		return View::make('core::admin.menus.item-edit', $this->data);
	}

	public function attempt_item_edit($id)
	{
		$menu = MenuItem::find($id);
		$menu->child_menu_id = Input::get('child_menu_id');
		$menu->save();

		return Redirect::to(admin_uri('menus'))->with('success', '
			<p>Menu item settings successfully updated.</p>
		');
	}

	/**
	 * AJAX for deleting menu items
	 */
	public function item_delete()
	{
		$menu_item = MenuItem::findOrFail(Input::get('id'));
		$menu_id = $menu_item->menu_id;
		$menu_item->forceDelete();  // Don't soft delete.  These are easy to rebuild.  Additionally, restoring pages
									// will restore all menu links for that page, so we don't want
									// deliberately deleted links resurfacing.
		$this->reorder($menu_id);
		return 1;
	}

	/**
	 * AJAX for getting 'existing model' dropdown in menu link creation wizard.
	 *
	 * @return string - HTML of the select element
	 */
	public function model_drop_down()
	{
		$model = Input::get('model');
		$arr = LinkableModel::drop_down($model);
		if (!count($arr)) return 0;
		return Form::select('fid', $arr, null, array('id'=>'thingSelect', 'class' => 'form-control'));
	}
}