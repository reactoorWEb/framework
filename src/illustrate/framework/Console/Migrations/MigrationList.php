<?php

namespace illustrate\Console\Migrations;

use illustrate\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class Application Reactoor\illustrate
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package illustrate\Console\Migrations
 */
class MigrationList extends Command
{
	protected function configure()
	{
		$this->setName('migrate:list')
			->setDescription('list all migration in database');
	}

	protected function execute(InputInterface $input , OutputInterface $output)
	{
		glob(config('migrations', 'database').'*.php');
		$table = new Table($output);
		$table->setHeaders(['files', 'migrated']);
		$tables = [];
		$data = Application::$app->database->from('migrations')->column('migration');
		foreach (glob(config('migrations', 'database').'*.php') as $item) {
			if ($data == basename($item)){
				$tables[] = ['files' => basename($item), 'migrates' => '<fg=blue>OK</>'];
			}else{
				$tables[] = ['files' => basename($item), 'migrates' => '<fg=yellow>NO</>'];
			}
		}
		$table->setRows($tables);
		$table->render();
		return Command::SUCCESS;
	}
}