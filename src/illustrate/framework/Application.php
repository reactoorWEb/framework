<?php


namespace illustrate;


/*
* Application:Reactoor\Phoenix Team
* Date: 6/11/2023
* Creator: Arshiamohammadei
*/

use illustrate\Database\Connection;
use illustrate\Database\Database;
use illustrate\Config\ConfigHelper;
use illustrate\Container\Container;
use illustrate\Exeptions\Handler\PrettyPageHandler;
use illustrate\Exeptions\Run;
use illustrate\Blade;
use illustrate\ORM\EntityManager;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Finder\Finder;

class Application extends Container
{
    const EVENT_BEFORE_REQUEST = 'beforeRequest';
    const EVENT_AFTER_REQUEST = 'afterRequest';

    protected array $eventListeners = [];

	public static string $CONFIG_DIR = __DIR__.'/../../config';
    public static Application $app;
    public static string $ROOT_DIR = __DIR__;
    public string $userClass;
    public string $layout = 'main';
    public Router $router;
    public Request $request;
    public Response $response;
    public ?Controller $controller = null;
    public $database;
    public Session $session;
	public $config;
	public $configs;
	public EntityManager $orm;

	/**
	 * @param mixed $config
	 */
	public function setConfig($config): void
	{
		$this->config = $config;
	}
    public Blade $view;
    public ?UserModel $user;
    public function __construct($rootDir)
    {
	    $exeptions = new Run();
	    $exeptions->pushHandler(new PrettyPageHandler());
	    $exeptions->register();		$env = new Dotenv();
		$env->load($rootDir.'/.env');
		$find = new Finder();
		$find->files()->in($rootDir.'/config');
	    foreach ($find as $item) {
		    $config[$item->getFilename()] = new \illustrate\Config\ConfigHelper(include $item->getRealPath());
	    }
		$this->configs = $config;
        $this->user = null;
        $this->userClass = $config['database.php']->get('userClass');
        self::$ROOT_DIR = $rootDir;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->session = new Session();
        $this->view = new Blade();
        $userId = Application::$app->session->get('user');
		if ($userId) {
            $key = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$key => $userId]);
        }
		if ($config['database.php']->get('database') == null){
			return 0;
		}else{
			$database = new Connection(
				$config['database.php']->get('driver').":host=".$config['database.php']->get('host').";"."dbname="
				.$config['database.php']->get('database'),
				$config['database.php']->get('username'),
				$config['database.php']->get('password'),
				$config['database.php']->get('driver')
			);
			$this->database = new Database($database);
			$database->options(
				$config['database.php']->get('options')
			);
			$this->orm = new EntityManager($database);
		}
		$data_log = $this->database->getLog();
		if ($config['app.php']->get('log') == true){
			if (file_exists($config['app.php']->get('storage_log'))){
				file_put_contents($config['app.php']->get('storage_log'), $data_log,FILE_APPEND);
			}else{
				$file = fopen($config['app.php']->get('storage_log'), "w+");
				fwrite($file,$data_log[]);
				fclose($file);
			}
		}else{
			return 0;
		}
    }

    public static function isGuest()
    {
        return !self::$app->user;
    }

    public function login(UserModel $user)
    {
        $this->user = $user;
        $className = get_class($user);
        $primaryKey = $className::primaryKey();
        $value = $user->{$primaryKey};
        Application::$app->session->set('user', $value);
        return true;
    }

    public function logout()
    {
        $this->user = null;
        self::$app->session->remove('user');
    }

    public function run()
    {
        $this->triggerEvent(self::EVENT_BEFORE_REQUEST);
        try {
            echo $this->router->resolve();
        } catch (\Exception $e) {
            echo 'error 404 ';
        }
    }

    public function triggerEvent($eventName)
    {
        $callbacks = $this->eventListeners[$eventName] ?? [];
        foreach ($callbacks as $callback) {
            call_user_func($callback);
        }
    }

    public function on($eventName, $callback)
    {
        $this->eventListeners[$eventName][] = $callback;
    }
}