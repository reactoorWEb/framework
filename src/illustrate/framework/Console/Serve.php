<?php

namespace illustrate\Console;

/**
 * Class Application Reactoor\illustrate
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package illustrate\Container
 */

use illustrate\Helpers;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Pipes\WindowsPipes;
use Symfony\Component\Process\Process;
class Serve extends Command
{
	protected function configure()
	{
		$this->setName('serve')
			->setDescription('Start Application')
			->addArgument('host', InputArgument::OPTIONAL, 'The Host Name', '127.0.0.1')
			->addArgument('port', InputArgument::OPTIONAL, 'The Port Number', '8000')
			->addArgument('path', InputArgument::OPTIONAL, 'The Path Address','public/');
	}
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$inp = $input->getArgument('host');
		$io = new SymfonyStyle($input,$output);
		$inpp = $input->getArgument('port');
		$path = $input->getArgument('path');
		$io->newLine(2);
		$io->writeln('<fg=green>Compiled Code...');
		$pro = new ProgressBar($output,100);
		$pro->start(100);
		$pro->setMaxSteps(100);
		$pro->setFormat('verbose');
		$pro->clear();
		$pro->finish();
		$io->newLine(2);
		sleep(2);
		$io->writeln("<info><fg=magenta>Starting Reactoor development server</>:</info> <href=http://$inp:$inpp>http://$inp:$inpp</>");
		$command = (new PhpExecutableFinder())->find();
		$process = new Process([$command, '-S', "$inp:$inpp", '-t', $path]);
		$process->start();
		$process->setTimeout(false);
		$io->success('START => serve in reactoor server');
		if ($process->getErrorOutput() == ''){
			echo '';
		}else{
			$io->error($process->getErrorOutput());
		}
		$process->wait(function ($type, $buffer) {
			if (Process::ERR === $type) {
				$file = fopen(Helpers::config('storage_log', 'app'), 'w');
				fwrite($file,$buffer);
				fclose($file);
				echo 'START> '.$buffer;
			} else {
				$file = fopen(Helpers::config('storage_log', 'app'), 'w');
				fwrite($file,$buffer);
				fclose($file);
				echo 'OUTPUT > '.$buffer;
			}
		});
		return $process->getOutput();
	}
}