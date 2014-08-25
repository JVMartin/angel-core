<?php namespace Angel\Core;

use Illuminate\Console\Command;

class AngelUpdate extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'angel:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Do a composer update and publish all Angel package assets.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		chdir(base_path());
		$this->exec('composer update --prefer-dist');
		$this->exec('php artisan angel:assets');
	}

	private function exec($command)
	{
		$this->info('Executing: ' . $command);
		echo shell_exec($command);
	}

}
