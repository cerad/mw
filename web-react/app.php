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
     
    $this->registerRoutes($this->getContainer());
  }
  protected function registerRoutes($container)
  {
    $router = $container['router'];
    
    $this->get('/',function($request,$response) use ($container)
    {
      ob_start();
      require 'app.html';
      return $response->getBody()->write(ob_get_clean());
    });
  }
}
$app = new App();
$app->run();
