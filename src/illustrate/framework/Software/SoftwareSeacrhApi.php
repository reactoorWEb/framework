<?php

namespace illustrate\Software;

use illustrate\Helpers;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class Application Reactoor\illustrate
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package illustrate\Software
 */
class SoftwareSeacrhApi extends Command
{
	protected function configure()
	{
		$this->setName('software:search')->setDescription('Search templates in our database');
	}
	protected function execute(InputInterface $input , OutputInterface $output)
	{
		$io = new SymfonyStyle($input,$output);
		$search = $io->ask('What type of template do you want? Enter its name or category');
		$json_data_api = file_get_contents($_ENV['SOFTWARE_URL'].'/template/?key='.$_ENV['SOFTWARE_API_KEY'].'&search='.urlencode($search));
		$progressBar = new ProgressBar($output, 100);
		if (file_exists(Helpers::config('storage', 'software').'search/search.json')){
			$q = $io->ask('The cached files of the templates are already cached. Do you want to update?', 'no');
			if ($q == 'yes'){
				$progressBar->setFormat(
					"<fg=white;bg=cyan> %status:-45s%</>\n%current%/%max% [%bar%] %percent:3s%%\n?  %estimated:-20s%"
				);
				$progressBar->setBarCharacter('<fg=green>⚬</>');
				$progressBar->setEmptyBarCharacter("<fg=red>⚬</>");
				$progressBar->setProgressCharacter("<fg=green>➤</>");
				$progressBar->start();
				for ($i = 0; $i < 100; $i++) {
					$progressBar->setMessage($i, 'item'); // set the `item` value
					$progressBar->advance();
					usleep(1000);
				}
				$progressBar->finish();
				sleep(1);
				$io->newLine(3);
				$io->writeln('Your template list is ready, you can check the cache file manually or check the templates with the <fg=blue>software:list</> command.Cache:[<fg=yellow>'.realpath(Helpers::config('storage', 'software').'cache.json').'</>]');
				$file = fopen(Helpers::config('storage', 'software').'cache.json', 'w+');
				fwrite($file,$json_data_api);
				fclose($file);
			}else{
				$io->newLine(2);
				$io->writeln('<fg=green>GoodBye :)');
				$io->newLine(2);
				exit();
			}
		}else{
			$progressBar->setFormat(
				"<fg=white;bg=cyan> %status:-45s%</>\n%current%/%max% [%bar%] %percent:3s%%\n?  %estimated:-20s%"
			);
			$progressBar->setBarCharacter('<fg=green>⚬</>');
			$progressBar->setEmptyBarCharacter("<fg=red>⚬</>");
			$progressBar->setProgressCharacter("<fg=green>➤</>");
			$progressBar->start();
			for ($i = 0; $i < 100; $i++) {
				$progressBar->setMessage($i, 'item'); // set the `item` value
				$progressBar->advance();
				usleep(1000);
			}
			$progressBar->finish();
			$file = fopen(Helpers::config('storage', 'software').'search/search.json', 'w+');
			fwrite($file,$json_data_api);
			fclose($file);
			sleep(1);
			$io->newLine(3);
			$io->writeln('Your template list is ready, you can check the cache file manually or check the templates with the <fg=blue>software:list</> command.Cache:[<fg=yellow>'.realpath(Helpers::config('storage', 'software').'cache.json').'</>]');
		}
		return Command::SUCCESS;
	}
}