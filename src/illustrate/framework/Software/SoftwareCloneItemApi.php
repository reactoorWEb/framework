<?php

namespace illustrate\Software;

use Doctrine\DBAL\Types\StringType;
use illustrate\Helpers;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\ProgressIndicator;

/**
 * Class Application Reactoor\illustrate
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package illustrate\Software
 */
class SoftwareCloneItemApi extends Command
{
	protected function configure()
	{
		$this->setName('software:clone')
		->setDescription('Get and start designing and customizing the template');
	}
	protected function execute(InputInterface $input , OutputInterface $output)
	{
		$io = new SymfonyStyle($input,$output);
		$code = $io->ask('Enter the desired template or plugin code');
		$file = fopen(Helpers::config('storage', 'software').$code.'.json','w+');
		fwrite($file,file_get_contents($_ENV['SOFTWARE_URL'].'/template/?key='.$_ENV['SOFTWARE_API_KEY'].'&search=' .$code));
		fclose($file);
		$json_data = file_get_contents(Helpers::config('storage', 'software').$code.'.json');
		$json = json_decode($json_data);
		$io->writeln('<fg=red>                                        
         _____  ______          _____ _______ ____   ____  _____  
         |  __ \|  ____|   /\   / ____|__   __/ __ \ / __ \|  __ \ 
         | |__) | |__     /  \ | |       | | | |  | | |  | | |__) |
         |  _  /|  __|   / /\ \| |       | | | |  | | |  | |  _  / 
         | | \ \| |____ / ____ \ |____   | | | |__| | |__| | | \ \ 
         |_|  \_\______/_/    \_\_____|  |_|  \____/ \____/|_|  \_\
');
		$io->writeln('             <bg=blue>Hi:</><fg=green>'.$json->auth->name.'</> <fg=yellow>Welcome To Software Reactoor</>');
		$io->newLine(2);
		$po = $io->confirm('Do you want it to be in the view path of your program?');

		if ($po == 'yes'){
				foreach ($json->contact as $item) {
					$pross = new ProgressBar($output,100);
					$pross->start();
					copy($item->clone,Helpers::config('view', 'view').'/'.$item->name.'.'.pathinfo($item->clone, PATHINFO_EXTENSION));
					$pross->advance();
					$zip = new \ZipArchive();
					$open_Zip_name = Helpers::config('view', 'view').'/'.$item->name.'.'.pathinfo($item->clone,
						PATHINFO_EXTENSION);
					if ($zip->open($open_Zip_name) === TRUE) {
						$zip->extractTo(Helpers::config('view', 'view').'/');
						$zip->close();
						$io->newLine(2);
						$io->writeln('<fg=blue>'.$item->name.' [<fg=yellow>'.basename($item->clone).'</>] </>, <fg=green>the file has been successfully extracted in the views section of your application');
						$io->newLine(2);
						unlink($open_Zip_name);
					} else {
						$io->writeln('<fg=blue>'.$item->code.'</>, <fg=red>Error the unzip');
					}
					$io->newLine(2);
					$pross->finish();
			}
		}elseif ($po == 'no'){
			$pros = new ProgressBar($output,100);
			$pros->start();
			$path = $io->ask('Where do I save the selected template?');
			for ($i = 0; $i < 100; $i++){
				$pros->advance();
				foreach ($json->contact as $item) {
					copy($item->clone,$path.'/'.$item->name.pathinfo($item->clone));
					$zip = new \ZipArchive();
					$open_Zip_name = $path.'/'.$item->name.'.'.pathinfo($item->clone);
					if ($zip->open($open_Zip_name,
							\ZipArchive::CREATE)!==TRUE) {
						$io->newLine(2);
						$io->writeln('<fg=blue>'.$item->code.'</>, <fg=green>the file has been successfully extracted in the views section of your application in</> <fg=yellow>[ '. $path . ' ]</>');
					}
					$pros->finish();
				}
			}
		}
		foreach ($json->contact as $item) {
			$io->newLine(2);
			$io->section('Clone Theme: '.$item->name);
		}
		return Command::SUCCESS;
	}
}