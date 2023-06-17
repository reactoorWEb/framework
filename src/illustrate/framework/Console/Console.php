<?php

namespace illustrate\Console;

use App\Commands\Hello;
use illustrate\Console\Database\DatabaseShow;
use illustrate\Console\Database\DatabaseTable;
use illustrate\Console\Database\DatabaseWipe;
use illustrate\Console\Maker\Controller;
use illustrate\Console\Maker\MakeMigration;
use illustrate\Console\Maker\Middleware;
use illustrate\Console\Maker\Model;
use illustrate\Console\Migrations\MigrationApply;
use illustrate\Console\Migrations\MigrationFresh;
use illustrate\Console\Migrations\MigrationList;
use illustrate\Console\Migrations\MigrationRefresh;
use illustrate\Console\View\ViewCache;
use illustrate\Console\View\ViewCompiled;
use illustrate\Software\AuthSoftwareApplication;
use illustrate\Software\ExecuteSoftwareItemApi;
use illustrate\Software\SoftwareCloneItemApi;
use illustrate\Software\SoftwareCloneListCodes;
use illustrate\Software\SoftwareListItemApi;
use illustrate\Software\SoftwareSeacrhApi;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command;
/**
 * Class Application Reactoor\illustrate
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package illustrate\Console
 */
class Console extends Application
{
	public $command = [];
	public function __construct()
	{
		return $this->command = [
			new Serve(),
			new ViewCache() ,
			new ViewCompiled(),
			new DatabaseWipe(),
			new DatabaseTable(),
			new Controller(),
			new Middleware(),
			new Model(),
			new RouteList(),
			new MigrationList(),
			new MakeMigration(),
			new MigrationApply(),
			new AuthSoftwareApplication(),
			new SoftwareSeacrhApi(),
			new SoftwareListItemApi(),
			new ExecuteSoftwareItemApi(),
			new SoftwareCloneItemApi(),
			new SoftwareCloneListCodes(),
			new MigrationFresh(),
			new MigrationRefresh()
		];
	}
}