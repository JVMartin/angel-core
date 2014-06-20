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
		App::singleton('Change', function() {
			return new \Angel\Core\Change;
		});
		App::singleton('Language', function() {
			return new \Angel\Core\Language;
		});
		App::singleton('Menu', function() {
			return new \Angel\Core\Menu;
		});
		App::singleton('MenuItem', function() {
			return new \Angel\Core\MenuItem;
		});
		App::singleton('Page', function() {
			return new \Angel\Core\Page;
		});
		App::singleton('PageModule', function() {
			return new \Angel\Core\PageModule;
		});
		App::singleton('Setting', function() {
			return new \Angel\Core\Setting;
		});

		//-------------------
		// Back-End Controllers
		//-------------------
		App::singleton('AdminLanguageController', function() {
			return new \Angel\Core\AdminLanguageController;
		});
		App::singleton('AdminMenuController', function() {
			return new \Angel\Core\AdminMenuController;
		});
		App::singleton('AdminMenuItemController', function() {
			return new \Angel\Core\AdminMenuItemController;
		});
		App::singleton('AdminPageController', function() {
			return new \Angel\Core\AdminPageController;
		});
		App::singleton('AdminSettingController', function() {
			return new \Angel\Core\AdminSettingController;
		});
		App::singleton('AdminUserController', function() {
			return new \Angel\Core\AdminUserController;
		});

		//-------------------
		// Front-End Controllers
		//-------------------
		App::singleton('PageController', function() {
			return new \Angel\Core\PageController;
		});
		App::singleton('UserController', function() {
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
