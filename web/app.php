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
      ob_start();
      require 'views/index.html.php';
      return $response->getBody()->write(ob_get_clean());
    });
  }
}
$app = new App();
$app->run();
