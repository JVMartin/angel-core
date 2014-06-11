<?php

class Setting extends Eloquent {

	protected $primaryKey = 'key';

	public static function settings() {
		return array(
			'title'	=> array(
				'value' => 'Your Company'
			),
			'theme' => array(
				'value' => 'default',
				'arr'	=> array(
					'default'	=> 'Default',
					'slate'		=> 'Slate'
				)
			)
		);
	}

	public static function currentSettings() {
		$settings = Setting::settings();

		foreach (Setting::all() as $setting) {
			$settings[$setting->key]['value'] = $setting->value;
		}

		return $settings;
	}

}