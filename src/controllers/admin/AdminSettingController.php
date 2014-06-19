<?php namespace Angel\Core;

use View, App, Input, Redirect, Config;

class AdminSettingController extends AdminAngelController {

	public function index()
	{
		return View::make('core::admin.settings.index', $this->data);
	}

	public function update()
	{
		$settingModel = App::make('Setting');

		foreach ($settingModel::currentSettings() as $key=>$setting) {
			if (!Input::exists($key) || Input::get($key) === $setting['value']) continue;
			$setting = $settingModel::find($key);
			if (!$setting) {
				$setting = new $settingModel;
				$setting->key = $key;
			}
			$setting->value = Input::get($key);
			$setting->save();
		}
		return Redirect::to(Config::get('core::admin_prefix') . '/settings')->with('success', 'Settings updated.');
	}

}