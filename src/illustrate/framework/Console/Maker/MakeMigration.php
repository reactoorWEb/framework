<?php

namespace illustrate\Console\Maker;

use illustrate\Helpers;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class Application Reactoor\illustrate
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package illustrate\Console\Maker
 */
class MakeMigration extends Command
{
	protected function configure()
	{
		$this->setName('make:migration')
		->setDescription('create new migration for database')
		->addArgument('name', InputArgument::OPTIONAL);
	}
	protected function execute(InputInterface $input , OutputInterface $output)
	{
		$io = new SymfonyStyle($input,$output);
		if ($input->getArgument('name') == null){
			$handle = $this->getHelper('question');
			$migrate = $io->ask('What is your Migration name?');
			//$model = new ChoiceQuestion('Aia Mikhahi model hm dorst knm?', ['yes', 'no'],'no');
			$name = "m" . rand(0,8963) . "_$migrate";
			//$s = $handle->ask($input,$output,$model);\
			$data = '<?php
use illustrate\Database\Schema\CreateTable;



class '.$name.'
{
	public function up()
	{
		$db=\illustrate\Application::$app->database;
		$db->schema()->create("'. $migrate .'", function(CreateTable $table){
			$table->integer("id");
			$table->timestamps();
		});
	}

	public function down()
	{
		$db=\illustrate\Application::$app->database;
		$db->schema()->drop("'.$migrate.'");
	}
}';
			$file= fopen(__DIR__.'/../../../../database/migrations/'.$name.".php", 'w+');
			fwrite($file,$data);
			fclose($file);
			$io->newLine(1);
			$io->writeln('<fg=green>Created Migration [<fg=blue>'.$name.".php"."</>]");
			$io->newLine(1);
		}else{
			$migrate = $input->getArgument('name');
			//$model = new ChoiceQuestion('Aia Mikhahi model hm dorst knm?', ['yes', 'no'],'no');
			$name = "m" . rand(0,8963) . "_$migrate";
			//$s = $handle->ask($input,$output,$model);\
			$data = '<?php
use illustrate\Database\Schema\CreateTable;




class '.$name.'
{
	public function up()
	{
		$db=\illustrate\Application::$app->database;
		$db->schema()->create("'. $migrate .'", function(CreateTable $table){
			$table->integer("id");
			$table->timestamps();
		});
	}

	public function down()
	{
		$db=\illustrate\Application::$app->database;
		$db->schema()->drop("'.$migrate.'");
	}
}';
			$file= fopen(__DIR__.'/../../../../database/migrations/'.$name.".php", 'w+');
			fwrite($file,$data);
			fclose($file);
			$io->newLine(1);
			$io->writeln('<fg=green>Created Migration [<fg=blue>'.$name.".php"."</>]");
			$io->newLine(1);
		}
	}
}