<?php

namespace illustrate\Console\Migrations;

use Illuminate\View\Compilers\Compiler;
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
class MigrationRefresh extends Command
{
	protected function configure()
	{
		$this->setName('migrate:refresh')->setDescription('Updating migrations and database tables');
	}
	protected function execute(InputInterface $input , OutputInterface $output)
	{
		$io = new SymfonyStyle($input,$output);
		$process = new Process(['php', 'reactoor', 'db:wipe']);
		$process->run();
		$io->writeln("<fg=green>".$process->getOutput()."</>");
		$io->writeln('Drop Tables: OK');
		sleep(2);
		$process = new Process(['php', 'reactoor', 'migrate:apply']);
		$process->run();
		$io->writeln("<fg=yellow>".$process->getOutput());
	}
}