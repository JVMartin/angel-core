<?php namespace Angel\Core;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
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
			array('file', InputArgument::OPTIONAL, 'The name of the file to load from.')
		);
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
		$this->info('Backing up MySQL database...');
		$host      = Config::get('database.connections.mysql.host');
		$database  = Config::get('database.connections.mysql.database');
		$username  = Config::get('database.connections.mysql.username');
		$password  = Config::get('database.connections.mysql.password');
		$file      = ($this->argument('file')) ? $this->argument('file') : $database . '.sql';
		chdir(base_path());
		$this->exec('mysqldump -h ' . $host . ' -u ' . $username . ' -p\'' . $password . '\' ' . $database . ' > ' . $file);
		$this->info('...finished.  Dump placed in ' . base_path() . '/' . $file);
	}

	private function exec($command)
	{
		$this->info('Executing: ' . $command);
		echo shell_exec($command);
	}
}
