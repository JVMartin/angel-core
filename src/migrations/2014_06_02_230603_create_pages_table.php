<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pages', function(Blueprint $table) {
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name');
			$table->string('url');
			$table->text('html');
			$table->text('plaintext');
			$table->text('js');
			$table->text('css');
			$table->string('title');
			$table->string('meta_description');
			$table->string('meta_keywords');
			$table->string('og_type');
			$table->string('og_image');
			$table->string('twitter_card');
			$table->string('twitter_image');
			$table->boolean('published')->default(1);
			$table->boolean('published_range')->default(0);
			$table->timestamp('published_start')->nullable();
			$table->timestamp('published_end')->nullable();
			$table->timestamps(); // Adds `created_at` and `updated_at` columns
		});

		if (ToolBelt::mysql_greater('5.6.4')) {
			DB::statement('ALTER TABLE `pages` ADD FULLTEXT search(`name`, `url`, `plaintext`, `meta_description`, `meta_keywords`)');
		}

		// Create the home page
		$Page = App::make('Page');
		$page = new $Page;
		$page->skipEvents = true;
		$page->url        = 'home';
		$page->name       = 'Home';
		$page->html       = '
			<h1>Welcome!</h1>

			<p>This is the default <a href="https://github.com/JVMartin/angel">Angel CMS</a> home page.</p>

			<p>Some good first steps to take:</p>

			<ul>
				<li>
					Change the superadmin password and email <a href="/admin/users/edit/1">from the users module here</a>.<br />
					Login using:
					<ul>
						<li>Username: avadmin</li>
						<li>Password: password</li>
					</ul>
				</li>
				<li><a href="/admin/pages/edit/1">Edit this default home page here</a>.</li>
			</ul>

		';
		$page->save();
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pages');
	}

}