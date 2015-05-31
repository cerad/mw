<?php
error_reporting(E_ALL);

require __DIR__  . '/../vendor/autoload.php';

use Cerad\Component\Dbal\ConnectionFactory;
use Cerad\Module\UserModule\UserRepository;

class App extends Slim\App
{
  public function __construct()
  {
    parent::__construct();
    
    $this->registerServices($this->getContainer());
    
    $this->registerRoutes($this->getContainer());
  }
  protected function registerServices($container)
  {
    $container['db_url_tests']  = 'mysql://tests:tests@localhost/tests';
    $container['db_conn_tests'] = function($container)
    {
      return ConnectionFactory::create($container['db_url_tests']);
    };
    $container['user_repository'] = function($container)
    {
      return new UserRepository($container['db_conn_tests']);
    };
  }
  protected function registerRoutes($container)
  {
    $router = $container['router'];
    
    $this->get('/',function($request,$response) use ($container)
    {
      $dbConn = $container['db_conn_tests'];
      $sql = 'SELECT user_name,disp_name FROM users';
      $users = $dbConn->executeQuery($sql)->fetchAll();
      
      ob_start();
      require 'views/index.html.php';
      return $response->getBody()->write(ob_get_clean());
    });
    $this->get('/users',function($request,$response) use ($container)
    {
      $userRepo = $container['user_repository'];
      $users = $userRepo->findAll();
      
      ob_start();
      require 'views/users.html.php';
      return $response->getBody()->write(ob_get_clean());
    });
  }
}

$app = new App();
$app->run();

/*
use Cerad\Component\HttpKernel\KernelApp;
use Cerad\Component\HttpMessage\Request;

use Cerad\Component\DependencyInjection\Container;

use Cerad\Module\UserModule\UserParameters;
use Cerad\Module\UserModule\UserServices;
use Cerad\Module\UserModule\UserRoutes;

class UserApp extends KernelApp
{
  protected function registerServices(Container $container)
  {
    parent::registerServices($container);

    new UserParameters($container);
    new UserServices  ($container);
    new UserRoutes    ($container);
  }
}

$app = new UserApp();

$request  = new Request($_SERVER);
$response = $app->handle($request);
$response->send();

*/