<?php
error_reporting(E_ALL);

require __DIR__  . '/../vendor/autoload.php';

use Cerad\Component\Dbal\ConnectionFactory;
use Cerad\Component\Cors\Cors;

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
    $container['cors'] = function($container)
    {
      return new Cors();
    };
  }
  protected function registerRoutes($container)
  {
    $router = $container['router'];
    $cors   = $container['cors'];
    
    $this->get('/users',function($request,$response) use ($container)
    {
      $userRepo = $container['user_repository'];
      $users  = $userRepo->findAll();
      $usersx = json_encode($users);
      
      $response->getBody()->write($usersx);
      $response = $response->withHeader('Content-Type','application/json; charset=utf-8');
      
      return $response;      
    })->add($cors);
  }
}
$app = new App();
$app->run();
