<?php

namespace illustrate\Console\View;

use illustrate\Helpers;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

/**
 * Class Application Reactoor\illustrate
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package illustrate\Console\View
 */
class ViewCache extends Command
{
	protected function configure()
	{
		$this->setName('view:clear')
			->setDescription('A command to clear the cache of framework views')
			->setHelp('A command to clear the cache of framework views');
	}
	protected function execute(InputInterface $input , OutputInterface $output)
	{
		$io = new SymfonyStyle($input,$output);
		$helper = $this->getHelper('question');
		$question = new ChoiceQuestion('Do you want to clear all cache memory?', ['yes', 'no'], 0);
		$bundle = $helper->ask($input, $output,$question);
		$question->setErrorMessage('It is not true: %s');
		if ($bundle == "no"){
			return true;
		}elseif ($bundle == "yes"){
			$find = new Finder();
			$find->files()->in(Helpers::config('storage', 'view'));
			foreach ($find as $file) {
				if (is_file($file)) {
					unlink($file);
					$output->writeln("<fg=green>[<fg=white>".$file->getFilename()."</>] cache memory cleared successfully :)");
				}
			}
		}
		return Command::SUCCESS;
	}
}