<?php

/*
* Application:Reactoor\Phoenix Team
* Date: 6/12/2023
* Creator: Arshiamohammadei
*/

namespace illustrate;

use illustrate\Blade;
use illustrate\Config\ConfigHelper;
use illustrate\Application;

/**
 * Class Application Phoenix
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package Reactoor\Phoenix
 */
class Helpers
{
	static function view($path,$data = []){
		$view = new Blade();
		return $view->make($path,$data);
	}
	static function config($name, $file)
	{
		$config = new ConfigHelper(include Application::$CONFIG_DIR.'/'.$file.'.php');
		return $config->get($name);
	}
}