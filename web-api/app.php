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
    
    $container = $this->getContainer();
    
    $this->registerServices($container);
    
    $this->registerRoutes($container);
    
    $this->add($container['cors']);
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
    $container['cors'] = function()
    {
      return new Cors();
    };
  }
  protected function registerRoutes($container)
  {
    $router = $container['router'];
    $cors   = $container['cors'];
    
    $router->map(['GET','OPTIONS'],'/api/users/{id}',function($request,$response) use ($container)
    {
      $route = $request->getAttribute('_route');
      
      $userRepo = $container['user_repository'];
      $userId = $request->getAttribute('id');
      $user  = $userRepo->findOne($userId);
      $user['_route_name'] = $route->getName(); //$request->getAttribute('_route_name');
      $userx = json_encode($user);
      
      $response->getBody()->write($userx);
      $response = $response->withHeader('Content-Type','application/json; charset=utf-8');
      
      return $response;      
    },[
      'template' => 'user.html.twig',
    ])->setName('api_users_one'); //->add($cors);
    $router->map(['GET','OPTIONS'],'/api/users',function($request,$response) use ($container)
    {
      $userRepo = $container['user_repository'];
      $users  = $userRepo->findAll();
      $usersx = json_encode($users);
      
      $response->getBody()->write($usersx);
      $response = $response->withHeader('Content-Type','application/json; charset=utf-8');
      
      return $response;      
    }); //->add($cors);
  }
}
$app = new App();
$app->run();
