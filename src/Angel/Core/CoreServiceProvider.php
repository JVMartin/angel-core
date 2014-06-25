<?php namespace Angel\Core;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;

class CoreServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('angel/core');

		include __DIR__ . '/Helpers.php';
		include __DIR__ . '/ToolBelt.php';
		include __DIR__ . '../../../routes.php';
		include __DIR__ . '../../../filters.php';

		$bindings = \Config::get('core::bindings');
		foreach ($bindings as $name=>$class) {
			App::singleton($name, function() use ($class) {
				return new $class;
			});
		}
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
