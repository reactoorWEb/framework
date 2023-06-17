<?php

namespace illustrate\Console\Migrations;

use illustrate\Application;
use illustrate\Console\Database\DatabaseWipe;
use illustrate\Database\Connection;
use illustrate\Database\Database;
use illustrate\Helpers;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\Cloner\Data;
use function Symfony\Component\Translation\t;

/**
 * Class Application Reactoor\illustrate
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package illustrate\Console\Migrations
 */
class MigrationApply extends Command
{
	private Application $application;

	protected function configure()
	{
		$this->setName('migrate:apply')->setDescription('apply migrations to database');
	}

	protected function execute(InputInterface $input , OutputInterface $output)
	{

		$connection = new Connection(
			Helpers::config('driver','database').":host=".Helpers::config('host','database').";"."dbname="
			.";"."dbname="
			.Helpers::config('database','database'),
			Helpers::config('username','database'),
			Helpers::config('password','database'),
			Helpers::config('driver','database')
		);
		$database = new Database($connection);
		$database->getConnection()->setLog($input,$output);
		$database->getConnection()->applyMigrations();
		return Command::SUCCESS;
	}
}