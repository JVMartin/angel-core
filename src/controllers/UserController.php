<?php namespace Angel\Core;

use Auth, View, Validator, Input, Redirect;

class UserController extends AngelController {

	public function signin()
	{
		return View::make('core::admin.signin', $this->data);
	}

	public function signout()
	{
		if (Auth::guest()) return Redirect::to(admin_uri());
		Auth::logout();
		return Redirect::to(admin_uri())->with('success', 'You have been signed out.');
	}

	public function attempt_signin()
	{
		Auth::logout();

		$rules = array(
			'loguser' => 'required',
			'logpass' => 'required'
		);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to(admin_uri())->withInput()->withErrors($validator);
		}

		// Users can use either their username or their email to login, so
		// we'll have to do 2 checks.
		$usernameCheck = array(
			'username' => Input::get('loguser'),
			'password' => Input::get('logpass')
		);
		$emailCheck = array(
			'email'    => Input::get('loguser'),
			'password' => Input::get('logpass')
		);

		if (Auth::attempt($usernameCheck, Input::get('remember')) ||
			Auth::attempt($emailCheck, Input::get('remember'))) {
			return Redirect::intended(admin_uri());
		}

		return Redirect::to(admin_uri())->withInput()->withErrors('Login attempt failed.');
	}

}