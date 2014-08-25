<?php namespace Angel\Core;

use Illuminate\Support\ServiceProvider;

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

		$this->app->bind('angel::command.db.backup', function() {
			return new DatabaseBackup;
		});
		$this->app->bind('angel::command.db.restore', function() {
			return new DatabaseRestore;
		});
		$this->app->bind('angel::command.angel.assets', function() {
			return new AngelAssets;
		});
		$this->app->bind('angel::command.angel.update', function() {
			return new AngelUpdate;
		});
		$this->commands(array(
			'angel::command.db.backup',
			'angel::command.db.restore',
			'angel::command.angel.assets',
			'angel::command.angel.update'
		));

		$bindings = array(
			// Models
			'Change'                  => '\Angel\Core\Change',
			'Language'                => '\Angel\Core\Language',
			'Link'                    => '\Angel\Core\Link',
			'Menu'                    => '\Angel\Core\Menu',
			'MenuItem'                => '\Angel\Core\MenuItem',
			'Page'                    => '\Angel\Core\Page',
			'PageModule'              => '\Angel\Core\PageModule',
			'Setting'                 => '\Angel\Core\Setting',

			// Controllers
			'AdminLanguageController' => '\Angel\Core\AdminLanguageController',
			'AdminLinkController'     => '\Angel\Core\AdminLinkController',
			'AdminMenuController'     => '\Angel\Core\AdminMenuController',
			'AdminMenuItemController' => '\Angel\Core\AdminMenuItemController',
			'AdminPageController'     => '\Angel\Core\AdminPageController',
			'AdminSettingController'  => '\Angel\Core\AdminSettingController',
			'AdminUserController'     => '\Angel\Core\AdminUserController',
			'PageController'          => '\Angel\Core\PageController',
			'UserController'          => '\Angel\Core\UserController'
		);

		foreach ($bindings as $name=>$class) {
			$this->app->singleton($name, function() use ($class) {
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
