<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table) {
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('type');
			$table->string('email');
			$table->string('username');
			$table->string('first_name');
			$table->string('last_name');
			$table->string('password');
			$table->string('remember_token')->nullable();
			$table->timestamps(); // Adds `created_at` and `updated_at` columns

			$table->unique('email');
			$table->unique('username');
		});

		// Create the admin
		DB::table('users')->insert(array(
			'type'       => 'superadmin',
			'username'   => 'avadmin',
			'first_name' => 'Angel',
			'last_name'  => 'Vision',
			'email'      => 'nobody@angelvisiontech.com',
			'password'   => Hash::make('password'),
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now()
		));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
