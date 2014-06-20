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
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//-------------------
		// Models
		//-------------------
		App::bind('Change', function() {
			return new \Angel\Core\Change;
		});
		App::bind('Language', function() {
			return new \Angel\Core\Language;
		});
		App::bind('Menu', function() {
			return new \Angel\Core\Menu;
		});
		App::bind('MenuItem', function() {
			return new \Angel\Core\MenuItem;
		});
		App::bind('Page', function() {
			return new \Angel\Core\Page;
		});
		App::bind('PageModule', function() {
			return new \Angel\Core\PageModule;
		});
		App::bind('Setting', function() {
			return new \Angel\Core\Setting;
		});

		//-------------------
		// Back-End Controllers
		//-------------------
		App::bind('AdminLanguageController', function() {
			return new \Angel\Core\AdminLanguageController;
		});
		App::bind('AdminMenuController', function() {
			return new \Angel\Core\AdminMenuController;
		});
		App::bind('AdminMenuItemController', function() {
			return new \Angel\Core\AdminMenuItemController;
		});
		App::bind('AdminPageController', function() {
			return new \Angel\Core\AdminPageController;
		});
		App::bind('AdminSettingController', function() {
			return new \Angel\Core\AdminSettingController;
		});
		App::bind('AdminUserController', function() {
			return new \Angel\Core\AdminUserController;
		});

		//-------------------
		// Front-End Controllers
		//-------------------
		App::bind('PageController', function() {
			return new \Angel\Core\PageController;
		});
		App::bind('UserController', function() {
			return new \Angel\Core\UserController;
		});
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
