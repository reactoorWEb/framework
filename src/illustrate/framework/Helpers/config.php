<?php

/**
 * Application:Reactoor\illustrate Team
 * date: 6/16/2023
 * @author: Arshiamohammadei
 */
use illustrate\Application;
use illustrate\Blade;

function config(string $name, string $file){
	$file = $file.'.php';
	return Application::$app->configs[$file]->get($name);
}
function view(string $path,array $data = []){
	return Application::$app->view->make($path,$data);
}