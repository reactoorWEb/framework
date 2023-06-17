<?php

namespace illustrate\Console\Maker;

use illustrate\Helpers;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class Application Reactoor\illustrate
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package illustrate\Console\Maker
 */
class Controller extends \Symfony\Component\Console\Command\Command
{
	protected function configure()
	{
		$this->setName('make:controller')
		->setDescription('Building a controller for server operations')
		->addArgument('file', InputArgument::OPTIONAL, 'What is your Controller name?');
	}
	protected function execute(InputInterface $input , OutputInterface $output)
	{
		$io = new SymfonyStyle($input, $output);
		if ($input->getArgument('file') == ''){
			$io->warning('You have not entered the controller name');
			$name = $io->ask('What is your Controller name?', 'home');
			$question = new ChoiceQuestion('What is your control type?', ['0' =>'view'], '0');
			$type = $io->askQuestion($question);
			if (file_exists(Helpers::config('path_app', 'app'). 'Controller/'.$name.'.php')){
				$access = $io->ask('This ['. $name .'] control is available. Want to replace it?', 'yes/no');
				if ($access == 'yes'){
					$data = '<?php


namespace App\Controllers;


use illustrate\Controller;


class ' . $name . ' extends Controller
{
     //Code...
}
			';
					$file = fopen(Helpers::config('path_app', 'app'). 'Controller/'.$name.'.php' , 'w+');
					fwrite($file, $data);
					fclose($file);
					$io->writeln('<fg=green>'. "<fg=magenta>". realpath(Helpers::config('path_app', 'app')). '/Controller/'
						.$name.'.php' . "</>" . ' control was designed successfully');
				}else{
					exit();
				}
			}else{
				$data = '<?php


namespace App\Controllers;


use illustrate\Controller;


class ' . $name . ' extends Controller
{
     //Code...
}
			';
				$file = fopen(Helpers::config('path_app', 'app'). 'Controller/'.$name.'.php' , 'w+');
				fwrite($file, $data);
				fclose($file);
				$io->writeln('<fg=green>'. "<fg=magenta>". realpath(Helpers::config('path_app', 'app')). '/Controller/'
					.$name.'.php' . "</>" . ' control was designed successfully');
			}
			}else{
			$data = '<?php


namespace App\Controllers;


use illustrate\Controller;


class ' . $input->getArgument('name') . ' extends Controller
{
     //Code...
}
			';
			$file = fopen(Helpers::config('path_app', 'app'). 'Controller/'.$input->getArgument('name').'.php' , 'w+');
			fwrite($file, $data);
			fclose($file);
			$io->writeln('<fg=green>'. "<fg=magenta>". realpath(Helpers::config('path_app', 'app')). '/Controller/'
				.$input->getArgument('name').'.php' . "</>" . ' control was designed successfully');
		}

		return \Symfony\Component\Console\Command\Command::SUCCESS;
	}
}