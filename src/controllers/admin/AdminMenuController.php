<?php namespace Angel\Core;

use App, Config, View, Form, Redirect;

class AdminMenuController extends AdminCrudController {

	protected $Model	= 'Menu';
	protected $uri		= 'menus';
	protected $plural	= 'menus';
	protected $singular	= 'menu';
	protected $package	= 'core';

	public function index()
	{
		$Menu = App::make('Menu');

		$paginator = $Menu::with('menuItems')->paginate(5);

		$model_list = array();
		$this->data['linkable_models'] = array();

		foreach (Config::get('core::linkable_models') as $Model=>$uri) {
			$model_list[$Model] = $Model;
			$this->data['linkable_models'][$Model] = array(
				'add' => admin_url($uri.'/add')
			);
			$arr = with(App::make($Model))->lists('name', 'id');
			if (count($arr)) {
				$this->data['linkable_models'][$Model]['select'] = Form::select('fid', $arr, null, array('class' => 'form-control'));
			} else {
				$this->data['linkable_models'][$Model]['select'] = 0;
			}
		}
		
		$this->data['model_select'] = Form::select('fmodel', $model_list, null, array('id'=>'modelSelect', 'class' => 'form-control', 'autocomplete'=>'off'));
		$this->data['menus'] = $paginator->getCollection();
		$appends = $_GET;
		unset($appends['page']);
		$this->data['links'] = $paginator->appends($appends)->links();

		return View::make('core::admin.menus.index', $this->data);
	}

	public function delete($id, $ajax = null)
	{
		$Menu     = App::make('Menu');
		$MenuItem = App::make('MenuItem');
		$menu     = $Menu::findOrFail($id);

		if ($MenuItem::where('child_menu_id', $menu->id)->count()) {
			return Redirect::to($this->uri('edit/' . $menu->id))->withErrors('
				You cannot delete a menu while it is another menu\'s child.
			');
		}

		if ($MenuItem::whereNotNull('child_menu_id')->where('menu_id', $menu->id)->count()) {
			return Redirect::to($this->uri('edit/' . $menu->id))->withErrors('
				You cannot delete a menu while it has child menus.
			');
		}

		return parent::delete($id);
	}
}