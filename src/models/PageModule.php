<?php namespace Angel\Core;

use App;

class PageModule extends \Eloquent {

	protected $table = 'pages_modules';

	public function page()
	{
		return $this->belongsTo(App::make('Page'));
	}

	///////////////////////////////////////////////
	//                  Events                   //
	///////////////////////////////////////////////
	public static function boot()
	{
		parent::boot();

		static::saving(function($module) {
			$module->plaintext = strip_tags($module->html);
		});
	}

}