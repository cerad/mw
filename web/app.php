<?php
error_reporting(E_ALL);

require __DIR__  . '/../vendor/autoload.php';

use Cerad\Component\Dbal\ConnectionFactory;
use Cerad\Module\UserModule\UserRepository;

use Cerad\Middleware\RequestAttributes;

class App extends Slim\App
{
  public function __construct()
  {
    parent::__construct();
    
    $this->registerServices($this->getContainer());
    
    $this->registerRoutes($this->getContainer());
  }
  protected function registerServices($dic)
  {
    $dic['db_url_tests']  = 'mysql://tests:tests@localhost/tests';
    $dic['db_conn_tests'] = function($dic)
    {
      return ConnectionFactory::create($dic['db_url_tests']);
    };
    $dic['user_repository'] = function($dic)
    {
      return new UserRepository($dic['db_conn_tests']);
    };
  }
  protected function registerRoutes($dic)
  {
    $router = $dic->get('router');
    
    $router->map(['GET'],'/',function($request,$response) use ($dic)
    {
      $routeName = $request->getAttribute('route_name');
      
      ob_start();
      require $request->getAttribute('template_name'); //'views/index.html.php';
      return $response->getBody()->write(ob_get_clean());
      
    })->setName('app_index')
      ->add(new RequestAttributes([
        'route_name' => 'app_index',
        'template_name' => 'views/index.html.php'
      ])
    );
  }
}
$app = new App();
$app->run();
