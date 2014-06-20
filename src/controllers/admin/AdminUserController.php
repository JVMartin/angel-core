<?php namespace Angel\Core;

use User, Auth, Input, View, Session, Redirect, Validator;

class AdminUserController extends AdminAngelController {

	public function index()
	{
		$search = Input::get('search') ? urldecode(Input::get('search')) : null;

		$paginator = User::withTrashed();

		// Limit viewable types if not superadmin
		if (!Auth::user()->is_superadmin()) {
			$paginator->whereIn('type', array_keys($this->okay_types()));
		}

		if ($search) {
			$terms = explode(' ', $search);
			$paginator = $paginator->where(function($query) use ($terms) {
				foreach ($terms as $term) {
					$term = '%'.$term.'%';
					$query->orWhere('email', 'like', $term)
						  ->orWhere('username', 'like', $term)
						  ->orWhere('first_name', 'like', $term)
						  ->orWhere('last_name', 'like', $term);
				}
			});
		}
		$paginator = $paginator->paginate();

		$this->data['users'] = $paginator->getCollection();
		$appends = $_GET;
		unset($appends['page']);
		$this->data['links'] = $paginator->appends($appends)->links();
		$this->data['search'] = $search;

		return View::make('core::admin.users.index', $this->data);
	}

	public function add()
	{
		$this->data['action'] = 'add';
		$this->data['okay_types'] = $this->okay_types();
		return View::make('core::admin.users.add-or-edit', $this->data);
	}

	public function attempt_add()
	{
		$rules = array(
			'type'		=> 'required|in:'.$this->okay_types_csv(),
			'email' 	=> 'required|email|unique:users',
			'username'	=> 'required|between:4,16|unique:users',
			'first_name'=> 'alpha_dash',
			'last_name'	=> 'alpha_dash',
			'password'	=> 'required|min:6|confirmed'
		);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to(admin_uri('users/add'))->withInput()->withErrors($validator);
		}

		$user = new User;
		$user->type			= Input::get('type');
		$user->email		= Input::get('email');
		$user->username		= Input::get('username');
		$user->first_name	= Input::get('first_name');
		$user->last_name	= Input::get('last_name');
		$user->password		= Hash::make(Input::get('password'));
		$user->save();

		return Redirect::to(admin_uri('users'))->with('success', 'User successfully created.');
	}

	public function edit($id)
	{
		$user = User::withTrashed()->find($id);
		if (!$this->okay_user($user)) {
			return Redirect::to(admin_uri('users'))->withErrors('You don\'t have permission to edit that user.');
		}

		$this->data['action'] = 'edit';
		$this->data['edit_user'] = $user;
		$this->data['okay_types'] = $this->okay_types();
		return View::make('core::admin.users.add-or-edit', $this->data);
	}

	public function attempt_edit($id)
	{
		$rules = array(
			'type'		=> 'required|in:'.$this->okay_types_csv(),
			'email' 	=> 'required|email|unique:users,email,'.$id,
			'username'	=> 'required|between:4,16|unique:users,username,'.$id,
			'first_name'=> 'alpha_dash',
			'last_name'	=> 'alpha_dash'
		);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to(admin_uri('users/edit/' . $id))->withInput()->withErrors($validator);
		}

		$user = User::withTrashed()->find($id);
		if (!$this->okay_user($user)) {
			return Redirect::to(admin_uri('users'))->withErrors('You don\'t have permission to edit that user.');
		}

		$user->type			= Input::get('type');
		$user->email		= Input::get('email');
		$user->username		= Input::get('username');
		$user->first_name	= Input::get('first_name');
		$user->last_name	= Input::get('last_name');
		$user->save();

		return Redirect::to(admin_uri('users/edit/'.$id))->with('success', '
			<p>User successfully updated.</p>
			<p><a href="'.admin_url('users').'">Return to Users</a></p>
		');
	}

	public function attempt_edit_password($id)
	{
		$rules = array(
			'password'	=> 'required|min:6|confirmed'
		);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to(admin_uri('users/edit/'.$id))->withInput()->withErrors($validator);
		}

		$user = User::withTrashed()->find($id);
		if (!$this->okay_user($user)) {
			return Redirect::to(admin_uri('users'))->withErrors('You don\'t have permission to edit that user.');
		}

		$user->password	= Hash::make(Input::get('password'));
		$user->save();

		return Redirect::to(admin_uri('users/edit/'.$id))->with('success', '
			<p>User\'s password successfully reset.</p>
			<p><a href="'.admin_url('users').'">Return to Users</a></p>
		');
	}

	public function delete($id)
	{
		$user = User::find($id);
		if (!$this->okay_user($user)) {
			return Redirect::to(admin_uri('users'))->withErrors('You don\'t have permission to edit that user.');
		}

		$user->delete();
		return Redirect::to(admin_uri('users'))->with('success', '
			<p>User successfully deleted.</p>
			<p><a href="'.admin_url('users/restore/'.$user->id).'">Undo</a></p>
		');
	}

	public function hard_delete($id)
	{
		$user = User::withTrashed()->find($id);
		if (!$this->okay_user($user)) {
			return Redirect::to(admin_uri('users'))->withErrors('You don\'t have permission to edit that user.');
		}

		$user->forceDelete();
		return Redirect::to(admin_uri('users'))->with('success', '
			<p>User successfully deleted forever.</p>
		');
	}

	public function restore($id)
	{
		$user = User::withTrashed()->find($id);
		if (!$this->okay_user($user)) {
			return Redirect::to(admin_uri('users'))->withErrors('You don\'t have permission to edit that user.');
		}

		$user->restore();
		return Redirect::to(admin_uri('users'))->with('success', '
			<p>User successfully restored.</p>
		');
	}


	///////////////////////////////////////////////
	//              Permissions                  //
	///////////////////////////////////////////////
	/**
	 * Determine whether the current logged in user has permission to edit a specific user.
	 *
	 * @param User $user - The user being edited
	 * @return bool
	 */
	public function okay_user($user)
	{
		if (!Auth::user()->is_superadmin() && ($user->type == 'superadmin' || $user->type == 'admin')) return false;
		return true;
	}

	/**
	 * Depending on the user's type (admin, superadmin) - what types can be assigned to other users in add / edit?
	 *
	 * @return array - Array of types that the current user can edit
	 */
	public function okay_types()
	{
		$array = User::types_array();
		if (!Auth::user()->is_superadmin()) {
			unset($array['superadmin']);
			unset($array['admin']);
		}
		return $array;
	}

	/**
	 * Same as okay_types(), but in a comma separated list (for use in the validator)
	 *
	 * @return string - Comma separated list of types that the current user can edit
	 */
	public function okay_types_csv()
	{
		return implode(',', array_keys($this->okay_types()));
	}

}