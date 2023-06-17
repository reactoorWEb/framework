<?php

namespace illustrate\Console;

use illustrate\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class Application Reactoor\illustrate
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package illustrate\Console
 */
class RouteList extends Command
{
	public Application $application;
	protected function configure()
	{
		$this->setName('route:list')
		->setDescription('Show all program network routes');
	}
	protected function execute(InputInterface $input , OutputInterface $output)
	{
		global $app;
		$io = new SymfonyStyle($input,$output);
		$type = $io->ask('Which of the methods do you want to show?', 'all');
		if ($type == 'all'){
			dd($app->router->getRouteMap());
		}elseif ($type == 'get'){
			dd($app->router->getRouteMap()['get']);
		}elseif($type == 'post'){
			dd($app->router->getRouteMap()['post'] ?? 'not found :(');
		}else{
			dd($app->router->getRouteMap());
		}
	}
}