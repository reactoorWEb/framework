<?php

namespace illustrate\Software;

use illustrate\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Class Application Reactoor\illustrate
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package illustrate\Software
 */
class AuthSoftwareApplication extends Command
{
	protected function configure()
	{
		$this->setName('software:auth')
		->setDescription('Your api key:[<fg=red>'.  $_ENV['SOFTWARE_API_KEY']. '</>]');
	}
	protected function execute(InputInterface $input , OutputInterface $output)
	{
		$helper = $this->getHelper('question');
		$io = new SymfonyStyle($input,$output);
		$key = $_ENV['SOFTWARE_API_KEY'];
		$io->newLine(2);
		$io->writeln('Your key =>' . ' [<fg=blue>'.$key.'</>]');
		$io->newLine(1);
		$qustion = $io->ask('Do you want to design a new key?','yes/no');
		if ($qustion == 'yes'){
			$name = $io->ask('what is your name?','arshia');
			$url = env('SOFTWARE_URL').'/?name='.urlencode($name);
			$json_data = file_get_contents($url);
			$data = json_decode($json_data, true);
			$file_path =__DIR__.'/../../../.env';
			$config = parse_ini_file($file_path);
			$config['SOFTWARE_API_KEY'] = $data['reactoor']['api-key'];
			$ini_data = '';
			foreach ($config as $key => $value) {
				$ini_data .= "$key=$value\n";
			}
			file_put_contents($file_path, $ini_data);
			$io->newLine(2);
			sleep(1);
			$progressBar = new ProgressBar($output, 100);
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
			$io->writeln('<fg=green>Your API key has been created successfully, you can see it in the .env file (<fg=blue>SOFTWARE_API_KEY - line[10]</>)');
		}
		return Command::SUCCESS;
	}
}