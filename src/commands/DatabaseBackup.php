<?php namespace Angel\Core;

use Illuminate\Console\Command;
//use Symfony\Component\Console\Input\InputOption;
//use Symfony\Component\Console\Input\InputArgument;
use Config;

class DatabaseBackup extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'db:backup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Backup your MySQL database to a dump in the project root.';

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
		return array(
			//array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			//array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->info('Backing up MySQL database...');
		$database  = Config::get('database.connections.mysql.database');
		$username  = Config::get('database.connections.mysql.username');
		$password  = Config::get('database.connections.mysql.password');
		$command   = 'mysqldump -u ' . $username . ' -p' . $password . ' ' . $database . ' > ' . $database . '.sql';
		chdir(base_path());
		$this->info('Issuing command: ' . $command);
		exec($command);
		$this->info('...finished.  Dump placed in ' . base_path());
	}

}
