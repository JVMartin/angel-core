<?php

///////////////////////////////////////////////
//                  Admin                    //
///////////////////////////////////////////////

Route::group(array('prefix' => Config::get('core::admin_prefix'), 'before' => 'admin'), function() {
	Route::get('/', array(
		'uses' => 'AdminPageController@index'
	));

	//------------------------
	// AdminUserController
	//------------------------
	Route::group(array('prefix' => 'users'), function() {

		$controller = 'AdminUserController';

		Route::get('/', array(
			'uses' => $controller . '@index'
		));
		Route::get('add', array(
			'uses' => $controller . '@add'
		));
		Route::post('add', array(
			'before' => 'csrf',
			'uses' => $controller . '@attempt_add'
		));
		Route::get('edit/{id}', array(
			'uses' => $controller . '@edit'
		));
		Route::post('edit/{id}', array(
			'before' => 'csrf',
			'uses' => $controller . '@attempt_edit'
		));
		Route::post('password/{id}', array(
			'before' => 'csrf',
			'uses' => $controller . '@attempt_edit_password'
		));
		Route::post('delete/{id}', array(
			'before' => 'csrf',
			'uses' => $controller . '@delete'
		));
		Route::post('hard-delete/{id}', array(
			'before' => 'csrf',
			'uses' => $controller . '@hard_delete'
		));
		Route::get('restore/{id}', array(
			'uses' => $controller . '@restore'
		));
	});

	//------------------------
	// AdminSettingController
	//------------------------
	Route::group(array('prefix' => 'settings'), function() {

		$controller = 'AdminSettingController';

		Route::get('/', array(
			'uses' => $controller . '@index'
		));
		Route::post('/', array(
			'uses' => $controller . '@update'
		));
	});

	//------------------------
	// AdminLanguageController - mostly superadmin filters!
	//------------------------
	if (Config::get('core::languages')) {
		Route::group(array('prefix' => 'languages', 'before' => 'superadmin'), function() {

			$controller = 'AdminLanguageController';

			Route::get('/', array(
				'uses' => $controller . '@index'
			));
			Route::get('add', array(
				'uses' => $controller . '@add'
			));
			Route::post('add', array(
				'before' => 'csrf',
				'uses' => $controller . '@attempt_add'
			));
			Route::get('edit/{id}', array(
				'uses' => $controller . '@edit'
			));
			Route::post('edit/{id}', array(
				'before' => 'csrf',
				'uses' => $controller . '@attempt_edit'
			));
			Route::post('hard-delete/{id}', array(
				'before' => 'csrf',
				'uses' => $controller . '@hard_delete'
			));
		});
		// Any admin can change their active (editing) language... not just super admin
		Route::get('languages/make-active/{id}', array(
			'uses' => 'AdminLanguageController@make_active'
		));
	}

	//------------------------
	// AdminPageController
	//------------------------
	Route::group(array('prefix' => 'pages'), function() {

		$controller = 'AdminPageController';

		Route::get('/', array(
			'uses' => $controller . '@index'
		));
		Route::get('add', array(
			'uses' => $controller . '@add'
		));
		Route::post('add', array(
			'before' => 'csrf',
			'uses' => $controller . '@attempt_add'
		));
		Route::get('edit/{id}', array(
			'uses' => $controller . '@edit'
		));
		Route::post('edit/{id}', array(
			'before' => 'csrf',
			'uses' => $controller . '@attempt_edit'
		));
		Route::post('delete/{id}', array(
			'before' => 'csrf',
			'uses' => $controller . '@delete'
		));
		Route::post('hard-delete/{id}', array(
			'before' => 'csrf',
			'uses' => $controller . '@hard_delete'
		));
		Route::get('restore/{id}', array(
			'uses' => $controller . '@restore'
		));
		Route::post('copy', array(
			'before' => 'csrf',
			'uses' => $controller . '@copy'
		));
	});

	//------------------------
	// AdminMenuController
	//------------------------
	Route::group(array('prefix' => 'menus'), function() {

		$controller = 'AdminMenuController';

		Route::get('/', array(
			'uses' => $controller . '@index'
		));
		Route::get('add', array(
			'uses' => $controller . '@add'
		));
		Route::post('add', array(
			'before' => 'csrf',
			'uses' => $controller . '@attempt_add'
		));
		Route::get('edit/{id}', array(
			'uses' => $controller . '@edit'
		));
		Route::post('edit/{id}', array(
			'before' => 'csrf',
			'uses' => $controller . '@attempt_edit'
		));
		Route::post('delete/{id}', array(
			'before' => 'csrf',
			'uses' => $controller . '@delete'
		));
		Route::post('hard-delete/{id}', array(
			'before' => 'csrf',
			'uses' => $controller . '@hard_delete'
		));
		Route::get('restore/{id}', array(
			'before' => 'admin',
			'uses' => $controller . '@restore'
		));
		Route::post('item-add', array(
			'uses' => $controller . '@item_add'
		));
		Route::post('item-order', array( // AJAX
			'uses' => $controller . '@item_order'
		));
		Route::get('item-edit/{id}', array(
			'uses' => $controller . '@item_edit'
		));
		Route::post('item-edit/{id}', array(
			'before' => 'csrf',
			'uses' => $controller . '@attempt_item_edit'
		));
		Route::post('item-delete', array( // AJAX
			'uses' => $controller . '@item_delete'
		));
		Route::post('model-drop-down', array( // AJAX
			'uses' => $controller . '@model_drop_down'
		));
	});
});

///////////////////////////////////////////////
//                Front End                  //
///////////////////////////////////////////////

//------------------------
// UserController
//------------------------
Route::get('signin', array(
	'before' => 'nonadmin',
	'uses' => 'UserController@signin'
));
Route::post('signin', array(
	'before' => 'csrf',
	'uses' => 'UserController@attempt_signin'
));
Route::get('signout', array(
	'before' => 'auth',
	'uses' => 'UserController@signout'
));

// We need to ensure that this is the -absolute- last route, otherwise
// we'll get caught in it before the router reaches other packages.
// Thus far, wrapping it in an App::before seems to do the trick.
App::before(function() {
	if (Config::get('core::languages')) {
		Route::get('{language_uri}/{url}/{section?}', page_controller() . '@show_language');
	} else {
		Route::get('{url}/{section?}', page_controller() . '@show');
	}
});

App::missing(function($exception) {
	$controller = App::make(page_controller());
	return $controller->page_missing();
});