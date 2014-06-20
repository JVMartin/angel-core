<?php namespace Angel\Core;

use Eloquent, App, Config, View;

class Menu extends Eloquent {

	protected $softDelete = true;

	// Columns to update/insert on edit/add
	public static function columns()
	{
		$columns = array(
			'name'
		);
		if (Config::get('core::languages')) $columns[] = 'language_id';
		return $columns;
	}

	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function menuItems()
	{
		return $this->hasMany(App::make('MenuItem'))->with('childMenu')->orderBy('order', 'asc');
	}
	public function language()
	{
		return $this->belongsTo(App::make('Language'));
	}


	///////////////////////////////////////////////
	//               View-Related                //
	///////////////////////////////////////////////
	public static function display($id)
	{
		$menuModel = App::make('Menu');

		$menu = $menuModel::with('menuItems')->findOrFail($id);

		$models = $menuModel::get_models($menu->menuItems);

		return View::make('core::menus.render', array('models'=>$models));
	}

	///////////////////////////////////////////////
	//                  Other                    //
	///////////////////////////////////////////////
	/**
	 * Get the models referenced by the menu links.
	 * MenuItems reference foreign models with foreign keys.
	 * (fmodel, fid)  Because of this fact, we need to compile
	 * each of the models' groups of keys to optimize the DB calls into as few queries as
	 * possible by batching models together, then restructuring the result back into the proper order.
	 *
	 * @param Collection $menu_items - The collection of MenuItem models
	 * @return array - An ordered array of foreign models referenced by the passed MenuItems
	 */
	public static function get_models($menu_items)
	{
		// $fmodels will keep track of the order and batch together the foreign keys of the foreign models.
		$fmodels = array();
		foreach ($menu_items as $menu_item) {
			$fmodels[$menu_item->fmodel][$menu_item->order]['fid'] = $menu_item->fid;
			
			if ($menu_item->childMenu) {
				$fmodels[$menu_item->fmodel][$menu_item->order]['menu_children'] = static::get_models($menu_item->childMenu->menuItems);
			}
		}

		// Now, we take those batched groups of IDs and perform a single query for each model.
		// Then place the results into $models (the final, ordered array which we return)
		$models = array();
		foreach ($fmodels as $fmodel=>$fmodel_rows) {
			$fmodel = App::make($fmodel);

			$ids = array();
			foreach ($fmodel_rows as $fmodel_row) {
				$ids[] = $fmodel_row['fid'];
			}
			
			$temp_models = $fmodel::whereIn('id', $ids);
			if (Config::get('core::languages')) {
				$temp_models = $temp_models->with('language');
			}
			$temp_models = $temp_models->get();

			foreach ($fmodel_rows as $order=>$fmodel_row) {
				$models[$order] = $temp_models->find($fmodel_row['fid']);

				if (!empty($fmodel_row['menu_children'])) $models[$order]['menu_children'] = $fmodel_row['menu_children'];
				/*
				$temp_model = $temp_models->find($id);
				if (method_exists($temp_model, 'is_published') && !$temp_model->is_published()) continue;
				$models[$order] = $temp_model;
				*/
			}
		}

		ksort($models);

		return $models;
	}
}

?>