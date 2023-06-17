<?php

namespace illustrate\Console\Maker;

use illustrate\Application;
use illustrate\Exeptions\ForbiddenException;
use illustrate\Helpers;
use illustrate\middlewares\BaseMiddleware;
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
class Middleware extends \Symfony\Component\Console\Command\Command
{
	protected function configure()
	{
		$this->setName('make:middleware')
			->setDescription('Building a middleware for server operations')
			->addArgument('file', InputArgument::OPTIONAL, 'What is your middleware name?');
	}
	protected function execute(InputInterface $input , OutputInterface $output)
	{
		$io = new SymfonyStyle($input, $output);
		if ($input->getArgument('file') == ''){
			$io->warning('You have not entered the middleware name');
			$name = $io->ask('What is your middleware name?', 'Users');
			$question = new ChoiceQuestion('What is your middleware type?', ['0' =>'normal'], '0');
			$type = $io->askQuestion($question);
			if (file_exists(Helpers::config('path_app', 'app'). 'Middlewares/'.$name.'.php')){
				$access = $io->ask('This ['. $name .'] middleware is available. Want to replace it?', 'yes/no');
				if ($access == 'yes'){
					$data = '<?php


namespace illustrate\middlewares;


use illustrate\Application;
use illustrate\Exeptions\ForbiddenException;
use illustrate\middlewares\BaseMiddleware;


class '. $name .' extends BaseMiddleware
{
    protected array $actions = [];

    public function __construct($actions = [])
    {
        $this->actions = $actions;
    }

    public function execute()
    {
    //code...
    }
}';
					$file = fopen(Helpers::config('path_app', 'app'). 'Middlewares/'.$name.'.php' , 'w+');
					fwrite($file, $data);
					fclose($file);
					$io->writeln('<fg=green>'. "<fg=magenta>". realpath(Helpers::config('path_app', 'app')). '/Middlewares/'
						.$name.'.php' . "</>" . ' Middleware was designed successfully');
				}else{
					exit();
				}
			}else{
				$data = '<?php


namespace illustrate\middlewares;


use illustrate\Application;
use illustrate\Exeptions\ForbiddenException;
use illustrate\middlewares\BaseMiddleware;


class '. $name .' extends BaseMiddleware
{
    protected array $actions = [];

    public function __construct($actions = [])
    {
        $this->actions = $actions;
    }

    public function execute()
    {
    //code...
    }
}';
				$file = fopen(Helpers::config('path_app', 'app'). 'Middlewares/'.$name.'.php' , 'w+');
				fwrite($file, $data);
				fclose($file);
				$io->writeln('<fg=green>'. "<fg=magenta>". realpath(Helpers::config('path_app', 'app')). '/Middlewares/'
					.$name.'.php' . "</>" . ' Middleware was designed successfully');
			}
		}else{
			$data = '<?php


namespace illustrate\middlewares;


use illustrate\Application;
use illustrate\Exeptions\ForbiddenException;
use illustrate\middlewares\BaseMiddleware;


class '. $input->getArgument('name') .' extends BaseMiddleware
{
    protected array $actions = [];
    
    public function __construct($actions = [])
    {
        $this->actions = $actions;
    }

    public function execute()
    {
    //code...
    }
}';
			$file = fopen(Helpers::config('path_app', 'app'). 'Middlewares/'.$input->getArgument('name').'.php' , 'w+');
			fwrite($file, $data);
			fclose($file);
			$io->writeln('<fg=green>'. "<fg=magenta>". realpath(Helpers::config('path_app', 'app')). '/Middlewares/'
				.$input->getArgument('name').'.php' . "</>" . ' Middleware was designed successfully');
		}

		return \Symfony\Component\Console\Command\Command::SUCCESS;
	}
}