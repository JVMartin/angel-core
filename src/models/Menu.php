<?php namespace Angel\Core;

use Eloquent, App, View;

class Menu extends Eloquent {

	protected $softDelete = true;

	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function menuItems()
	{
		return $this->hasMany(App::make('MenuItem'))->with('childMenu', 'childMenu.childMenuItems')->orderBy('order', 'asc');
	}
	public function childMenuItems()
	{
		return $this->hasMany(App::make('MenuItem'))->orderBy('order', 'asc');
	}
	public function language()
	{
		return $this->belongsTo(App::make('Language'));
	}

	///////////////////////////////////////////////
	//               View-Related                //
	///////////////////////////////////////////////
	public function display()
	{
		$this->fillItems();

		return View::make('core::menus.render', array('menu' => $this));
	}

	public function fillItems()
	{
		$modelsToFetch = $this->modelsToFetch($this->menuItems);

		$models = array();
		foreach ($modelsToFetch as $modelToFetch=>$ids) {
			$iocModel = App::make($modelToFetch);
			$models[$modelToFetch] = $iocModel::whereIn('id', $ids)->get();
		}

		$this->menuItems->each(function($menuItem) use ($models) {
			$menuItem->model = $models[$menuItem->fmodel]->find($menuItem->fid);
			if ($menuItem->childMenu) {
				$menuItem->childMenu->menuItems = $menuItem->childMenu->childMenuItems->each(function($menuItem) use ($models) {
					$menuItem->model = $models[$menuItem->fmodel]->find($menuItem->fid);
				});
			}
		});
	}

	private function modelsToFetch($menuItems, $fetchModels = array(), $goDeeper = true)
	{
		foreach ($menuItems as $menuItem) {
			if (!isset($fetchModels[$menuItem->fmodel])) {
				$fetchModels[$menuItem->fmodel] = array();
			}
			if (!in_array($menuItem->fid, $fetchModels[$menuItem->fmodel])) {
				$fetchModels[$menuItem->fmodel][] = $menuItem->fid;
			}
			if ($goDeeper && $menuItem->childMenu) {
				$fetchModels = $this->modelsToFetch($menuItem->childMenu->childMenuItems, $fetchModels, false);
			}
		}
		return $fetchModels;
	}
}

?>