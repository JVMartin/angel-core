<?php namespace Angel\Core;

use Eloquent;

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
		$settings = static::settings();

		foreach (static::all() as $setting) {
			$settings[$setting->key]['value'] = $setting->value;
		}

		return $settings;
	}

}