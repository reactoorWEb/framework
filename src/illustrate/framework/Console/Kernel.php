<?php

namespace illustrate\Console;

use Symfony\Component\Console\Application;

/**
 * Class Application Reactoor\illustrate
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package illustrate\Console
 */
class Kernel extends Application
{
	public function handle($make)
	{
		parent::__construct('Reactoor Framework - ' . $_ENV[ 'APP_NAME' ] , $_ENV[ 'APP_VERSION' ]);
		foreach ($make as $item) {
			$this->add($item);
		}
		$this->run();
	}
}