<?php

namespace illustrate\Console\View;

use Illuminate\View\View;
use illustrate\Blade;
use illustrate\Helpers;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * Class Application Reactoor\illustrate
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package illustrate\Console\View
 */
class ViewCompiled extends Command
{
	protected function configure()
	{
		$this->setName('view:cache')
		->setDescription('Compiles all view code from scratch');
	}
	protected function execute(InputInterface $input , OutputInterface $output)
	{
		$find = new Finder();
		$blade = new Blade();
		$find->files()->in(Helpers::config('view', 'view'));
		foreach ($find as $item) {
			$blade->compiler()->compile($item->getRealPath());
			$output->writeln("<fg=green> ["."<fg=white>".$item->getFilename()."</>"."] Compiled successfully ");
		}
		return Command::SUCCESS;
	}
}