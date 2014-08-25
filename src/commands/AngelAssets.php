<?php namespace Angel\Core;

use Illuminate\Console\Command;

class AngelAssets extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'angel:assets';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Publish all Angel package assets.';

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
		if (!is_dir(base_path('vendor/angel'))) {
			$this->error('The vendor/angel directory does not exist.');
			return;
		}
		$this->info('Publishing Angel package assets...');
		chdir(base_path());
		foreach (glob(base_path('vendor/angel/*')) as $dir) {
			if (!is_dir($dir)) {
				$this->error('Not a directory: ' . $dir);
				continue;
			}
			preg_match('/vendor\/(.+)/', $dir, $matches);
			$package = $matches[1];
			$this->exec('php artisan asset:publish ' . $package);
		}
		$this->info('...all Angel package assets have been published.');
	}

	private function exec($command)
	{
		$this->info('Executing: ' . $command);
		echo shell_exec($command);
	}

}
