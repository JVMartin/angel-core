<?php namespace Angel\Core;

use User, Redirect;

class AdminUserController extends AdminCrudController {

	protected $Model	= 'User';
	protected $uri		= 'users';
	protected $plural	= 'users';
	protected $singular	= 'edit_user';
	protected $package	= 'core';

	public function delete($id, $ajax = false)
	{
		$user = User::find($id);

		$errors = $user->validate_custom();
		if (count($errors)) return Redirect::to(admin_uri('users'))->withErrors($errors);

		$user->delete();

		return Redirect::to(admin_uri('users'))->with('success', '
			<p>User successfully deleted.</p>
			<p><a href="'.admin_url('users/restore/'.$user->id).'">Undo</a></p>
		');
	}

	public function hard_delete($id)
	{
		$user = User::withTrashed()->find($id);

		$errors = $user->validate_custom();
		if (count($errors)) return Redirect::to(admin_uri('users'))->withErrors($errors);

		$user->forceDelete();

		return Redirect::to(admin_uri('users'))->with('success', '
			<p>User successfully deleted forever.</p>
		');
	}

	public function restore($id)
	{
		$user = User::withTrashed()->find($id);

		$errors = $user->validate_custom();
		if (count($errors)) return Redirect::to(admin_uri('users'))->withErrors($errors);

		$user->restore();

		return Redirect::to(admin_uri('users'))->with('success', '
			<p>User successfully restored.</p>
		');
	}

}