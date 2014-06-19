<?php namespace Angel\Core;

class UserController extends AngelController {

	public function signin()
	{
		return View::make('core::admin.signin', $this->data);
	}

	public function signout()
	{
		Session::flush();
		Auth::logout();
		return Redirect::to('signin')->with('success', 'You have been signed out.');
	}

	public function attempt_signin()
	{
		$rules = array(
			'loguser' => 'required',
			'logpass' => 'required'
		);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to('signin')->withInput()->withErrors($validator);
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

		if (Auth::attempt($usernameCheck) || Auth::attempt($emailCheck)) {
			if (Auth::user()->type == 'superadmin') {
				Session::put('superadmin', true);
				Session::put('admin', true);
				return Redirect::intended(admin_uri('/'));
			}
			if (Auth::user()->type == 'admin') {
				Session::put('admin', true);
				return Redirect::intended(admin_uri('/'));
			}
			return Redirect::intended('/');
		}

		return Redirect::to('signin')->withInput()->withErrors('Login attempt failed.');
	}

}