<?php

namespace illustrate\Software;

use illustrate\Helpers;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class Application Reactoor\illustrate
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package illustrate\Software
 */
class SoftwareCloneListCodes extends Command
{
	protected function configure()
	{
		$this->setName('software:codes')
		->setDescription('List of codes and names of your cached templates');
	}
	protected function execute(InputInterface $input , OutputInterface $output)
	{
		$json_data = file_get_contents(Helpers::config('storage', 'software').'cache.json');
		$json = json_decode($json_data, true);
		$io = new SymfonyStyle($input,$output);
		foreach ($json['contact'] as $item){
			$io->writeln('<fg=green>name: <fg=blue>'.$item['name'].'</><fg=red> code: </><fg=yellow>'.$item['code'].'</>');
		}
	}
}