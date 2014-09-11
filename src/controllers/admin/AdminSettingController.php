<?php namespace Angel\Core;

use View, App, Input, Redirect;

class AdminSettingController extends AngelController {

	public function index()
	{
		return View::make('core::admin.settings.index', $this->data);
	}

	public function update()
	{
		$Setting = App::make('Setting');

		foreach ($Setting::currentSettings() as $key=>$setting) {
			if (!Input::exists($key) || Input::get($key) === $setting['value']) continue;
			$setting = $Setting::find($key);
			if (!$setting) {
				$setting = new $Setting;
				$setting->key = $key;
			}
			$setting->value = Input::get($key);
			$setting->save();
		}
		return Redirect::to(admin_uri('settings'))->with('success', 'Settings updated.');
	}

}