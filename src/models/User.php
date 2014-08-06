<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

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