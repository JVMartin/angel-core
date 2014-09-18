<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends \Angel\Core\AngelModel implements UserInterface, RemindableInterface {

	public static function columns()
	{
		return array(
			'type',
			'email',
			'username',
			'first_name',
			'last_name',
			'password'
		);
	}

	public function validate_rules()
	{
		// If we're just changing the password...
		if (Input::exists('password') && !Input::exists('email')) {
			return array(
				'password' => 'required|min:6|confirmed'
			);
		}

		$rules = array(
			'type'       => 'required|in:'.static::okay_types_csv(),
			'email'      => 'required|email|unique:users,email,' . $this->id,
			'username'   => 'required|between:4,16|unique:users,username,' . $this->id,
			'first_name' => 'alpha_dash',
			'last_name'  => 'alpha_dash'
		);

		// If we're adding a user...
		if (Input::exists('password')) {
			$rules['password'] = 'required|min:6|confirmed';
		}

		return $rules;
	}

	public function validate_custom()
	{
		if (!$this->okay_user()) {
			return array('You don\'t have permission to edit this user.');
		}
		return array();
	}

	public function is_admin()
	{
		if (in_array($this->type, array('superadmin', 'admin'))) return true;
		return false;
	}

	public function is_superadmin()
	{
		if ($this->type === 'superadmin') return true;
		return false;
	}

	public function full_name()
	{
		return $this->first_name . ' ' . $this->last_name;
	}

	public function search($terms)
	{
		return static::where(function($query) use ($terms) {
			foreach ($terms as $term) {
				$query->orWhere('email', 'like', $term);
				$query->orWhere('username',  'like', $term);
				$query->orWhere('first_name',  'like', $term);
				$query->orWhere('last_name',  'like', $term);
			}
		})->get();
	}

	///////////////////////////////////////////////
	//                  Events                   //
	///////////////////////////////////////////////
	public static function boot()
	{
		parent::boot();

		static::saving(function($user) {
			if ($user->skipEvents) return;

			// If we're updating the password, hash it
			if (Input::exists('password')) {
				$user->password = Hash::make(Input::get('password'));
			}
		});
	}

	///////////////////////////////////////////////
	//                   Types                   //
	///////////////////////////////////////////////
	/**
	 * This is where you can add user types.
	 *
	 * @return array - All available types
	 */
	public static function types_array()
	{
		return array(
			'superadmin' => 'superadmin',
			'admin'      => 'admin',
			'user'       => 'user'
		);
	}

	/**
	 * Depending on the user's type (admin, superadmin) - what types can be assigned to other users in add / edit?
	 *
	 * @return array - Array of types that the current user can edit
	 */
	public static function okay_types()
	{
		$array = static::types_array();
		if (!Auth::user()->is_superadmin()) {
			unset($array['superadmin']); // Don't let non-superadmins create superadmins
		}
		return $array;
	}

	/**
	 * Same as okay_types(), but in a comma separated list (for use in the validator)
	 *
	 * @return string - Comma separated list of types that the current user can edit
	 */
	public static function okay_types_csv()
	{
		return implode(',', array_keys(static::okay_types()));
	}

	/**
	 * Okay for logged-in user to edit this user?
	 */
	public function okay_user()
	{
		if (!Auth::user()->is_superadmin() && $this->is_superadmin()) return false;
		return true;
	}

	///////////////////////////////////////////////
	//           Laravel Auth Defaults           //
	///////////////////////////////////////////////
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return $this->remember_token;
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName()
	{
		return 'remember_token';
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

}