<?php

namespace illustrate\Software;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class Application Reactoor\illustrate
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package illustrate\Software
 */
class ExecuteSoftwareItemApi extends Command
{
	protected function configure()
	{
		$this->setName('software:output')
			->setDescription('The output of zip caches and viewing templates and demos');
	}
	protected function execute(InputInterface $input , OutputInterface $output)
	{
		$file = file_get_contents(\illustrate\Helpers::config('storage', 'software').'/cache.json');
		$json = json_decode($file);
		$io = new SymfonyStyle($input,$output);
		$path = $io->ask('Where do you want me to save the demo files?');
		$demo = new ChoiceQuestion('Do you want to download the whole templates or just the demos?', ['all', 'demo'],
			'demo');
		$demso = $io->askQuestion($demo);
		if ($demso == 'demo'){
			$dems = $io->askQuestion(new ChoiceQuestion('Do you want the demo files to be pictures or links?',
				['image', 'link'], 'link'));
			if ($dems == 'image'){
				mkdir($path.'/'.$json->auth->name);
				foreach ($json->contact as $item){
					mkdir($path.'/'.$path.'/'.$json->auth->name.'/'.$item->code, 0777,true);
					copy($item->image, $path.'/'.$path.'/'.$json->auth->name.'/'.$item->code.'/'.$item->name.'.'
						.pathinfo
						($item->image,PATHINFO_EXTENSION));
				}
			}elseif ($dems == 'link'){
				mkdir($path.'/'.$json->auth->name);
				foreach ($json->contact as $item){
					$filess = fopen($path.'/'.$path.'/'.$json->auth->name.'/[ '.$item->code.' ]---'.$item->name.'.html',
						'w+');
					$data= '
					<!--
Application:Reactoor Team
Date: '. date("Y-m-d") .'
Creator: Arshiamohammadei
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>'.$item->name.'-'.$item->code.'</title>
</head>
<body>
<iframe width="100%" style="height:1000px;border: none"
        src="'.$item->demo.'"></iframe>
</body>
</html>
					';
					fwrite($filess, $data);
					fclose($filess);
				}
			}
		}elseif ($demso == 'all'){
			mkdir($path.'/'.$json->auth->name);
			foreach ($json->contact as $item){
				copy("$item->clone",$path.'/'.$path.'/'.$json->auth->name.'/'."$item->code.zip");
			}
		}
		return Command::SUCCESS;
	}
}