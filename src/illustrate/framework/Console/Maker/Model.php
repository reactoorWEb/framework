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
class Model extends \Symfony\Component\Console\Command\Command
{
	protected function configure()
	{
		$this->setName('make:model')
			->setDescription('Building a model for server operations')
			->addArgument('file', InputArgument::OPTIONAL, 'What is your model name?');
	}
	protected function execute(InputInterface $input , OutputInterface $output)
	{
		$io = new SymfonyStyle($input, $output);
		if ($input->getArgument('file') == ''){
			$io->warning('You have not entered the model name');
			$name = $io->ask('What is your model name?', 'Users');
			$question = new ChoiceQuestion('What is your Model type?', ['0' =>'normal'], '0');
			$type = $io->askQuestion($question);
			if (file_exists(Helpers::config('path_app', 'app'). 'Models/'.$name.'.php')){
				$access = $io->ask('This ['. $name .'] Model is available. Want to replace it?', 'yes/no');
				if ($access == 'yes'){
					$data = '<?php

namespace App\Models;


use illustrate\ORM\Entity;



class '. $name .' Entity 
{
    //code...
}					
';
					$file = fopen(Helpers::config('path_app', 'app'). 'Models/'.$name.'.php' , 'w+');
					fwrite($file, $data);
					fclose($file);
					$io->writeln('<fg=green>'. "<fg=magenta>". realpath(Helpers::config('path_app', 'app')). '/Models/'
						.$name.'.php' . "</>" . ' model was designed successfully');
				}else{
					exit();
				}
			}else{
				$data = '<?php

namespace App\Models;
use illustrate\Application;
use illustrate\Model;


class '. $name .' extends Model
{
 //Code...
}					
';
				$file = fopen(Helpers::config('path_app', 'app'). 'Models/'.$name.'.php' , 'w+');
				fwrite($file, $data);
				fclose($file);
				$io->writeln('<fg=green>'. "<fg=magenta>". realpath(Helpers::config('path_app', 'app')). '/Models/'
					.$name.'.php' . "</>" . ' Model was designed successfully');
			}
		}else{
			$data = '<?php

namespace App\Models;


use illustrate\Application;
use illustrate\Model;


class '. $input->getArgument('name') .' extends Model
{
 //Code...
}					
';
			$file = fopen(Helpers::config('path_app', 'app'). 'Modles/'.$input->getArgument('name').'.php' , 'w+');
			fwrite($file, $data);
			fclose($file);
			$io->writeln('<fg=green>'. "<fg=magenta>". realpath(Helpers::config('path_app', 'app')). '/Model/'
				.$input->getArgument('name').'.php' . "</>" . ' Model was designed successfully');
		}
		return \Symfony\Component\Console\Command\Command::SUCCESS;
	}
}