<?php

namespace illustrate\Console\Database;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Class Application Reactoor\illustrate
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package illustrate\Console\Database
 */
class DatabaseTable extends Command
{
	protected function configure()
	{
		$this->setName('db:table')
		->setDescription('Display records and table information')
		->addArgument('name', InputArgument::OPTIONAL, 'What is the name of the table?');
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
		$helper = $this->getHelper('question');
		if ($input->getArgument('name') == ''){
			$tables = [];
			foreach ($sm->listTables() as $item) {
				$tables[] = $item->getName();
			}
			$question = new ChoiceQuestion('Continue with this action?', $tables);
			if (!$helper->ask($input, $output, $question)) {
				return Command::SUCCESS;
			}
			$table_show[] = [$sm->listTableDetails($helper->getName())];
			$t = new \Symfony\Component\Console\Helper\Table($output);
			$t->setHeaders(['name', 'namespace' , 'columns','indexes', 'primaryKetName', 'uniqueConstraints', 'fkConstraints', 'options']);
			$t->setStyle('box-double');
			foreach ($sm->listTableDetails($helper->getName())->getColumns() as $column) {
				$a = $column;
			}
			$t->setRows([
				[$sm->listTableDetails($helper->getName())->getName(), $sm->listTableDetails($helper->getName())
					->getNamespaceName()?? 'null',[
						'a' => $a
				]]
			]);
			$t->render();
		}else{
			echo $input->getArgument('name');
		}
	}
}