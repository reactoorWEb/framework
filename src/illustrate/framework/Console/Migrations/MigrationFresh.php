<?php

namespace illustrate\Console\Migrations;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

/**
 * Class Application Reactoor\illustrate
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package illustrate\Console\Migrations
 */
class MigrationFresh extends Command
{
	protected function configure()
	{
		$this->setName('migrate:fresh')
		->setDescription('Fresh migrations and database tables');
	}
	protected function execute(InputInterface $input , OutputInterface $output)
	{
		$io = new SymfonyStyle($input,$output);
		$process = new Process(['php', 'reactoor', 'db:wipe']);
		$process->run();
		$io->writeln("<fg=green>".$process->getOutput()."</>");
		$io->writeln('Drop Tables: OK');
	}
}