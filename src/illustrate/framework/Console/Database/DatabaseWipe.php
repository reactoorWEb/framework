<?php

namespace illustrate\Console\Database;

use Doctrine\DBAL\DriverManager;
use illustrate\Application;
use MongoDB\Driver\Exception\CommandException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class Application Reactoor\illustrate
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package illustrate\Console\Database
 */
class DatabaseWipe extends Command
{
	protected Application $application;
	protected function configure()
	{
		$this->setName('db:wipe')
		->setDescription('Delete all database tables and columns');
	}
	protected function execute(InputInterface $input , OutputInterface $output)
	{
		$connect = [
			'dbname' => $_ENV['DATABASE_NAME'],
			'user' => $_ENV['DATABASE_USER'],
			'password' => $_ENV['DATABASE_PASS'],
			'host' => $_ENV['DATABASE_HOST'],
			'driver' => $_ENV['DATABASE_DRIVER_DBALING'],
		];
		$argc = DriverManager::getConnection($connect);
		$sm = $argc->createSchemaManager();
		$io = new SymfonyStyle($input,$output);
		if (count($sm->listTables()) > 0){
			$helper = $this->getHelper('question');
			$question = new ChoiceQuestion('Are you sure you want to delete all database '. $_ENV['DATABASE_NAME'] .' tables (this action is irreversible)?', ['yes',	'no'],	0);
			$bundle = $helper->ask($input, $output,$question);
			if ($bundle == "no"){
				return true;
			}else{
				foreach ($sm->listTables() as $table) {
					$io->writeln('<fg=bright-red>[<fg=white>'. $table->getName() .'</>] Deleted :/');
					$sm->dropTable($table->getName());
				}
			}
		}else{
			$io->newLine(1);
			$io->writeln('<fg=cyan> There are no tables available :/');
		}
		return Command::SUCCESS;
	}
}