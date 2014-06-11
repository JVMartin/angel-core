<?php

Route::filter('auth', function () {
	if (Auth::guest()) return Redirect::guest('signin');
});

Route::filter('admin', function () {
	if (Auth::guest()) return Redirect::guest('signin');
	if (!Session::get('admin')) return Redirect::guest('signin')->withErrors('You must be logged in as an administrator to view that page.');
});

Route::filter('nonadmin', function () {
	if (Auth::check() && Session::get('admin')) return Redirect::to(admin_uri('/'));
});

Route::filter('superadmin', function () {
	if (Auth::guest()) return Redirect::guest('signin');
	if (!Session::get('superadmin')) {
		return Redirect::to(admin_uri('/'))->withErrors('You must be logged in as a super administrator to view that page.');
	}
});