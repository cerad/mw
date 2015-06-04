<?php
error_reporting(E_ALL);

require __DIR__  . '/../vendor/autoload.php';

use Cerad\Component\Dbal\ConnectionFactory;
use Cerad\Module\UserModule\UserApiController;
use Cerad\Module\UserModule\UserRepository;

use Cerad\Component\Cors\Cors;
use Cerad\Middleware\RequestAttributes;

class App extends Slim\App
{
  public function __construct()
  {
    parent::__construct();
    
    $dic = $this->getContainer();
    
    $this->registerServices($dic);
    
    $this->registerRoutes($dic);
    
    $this->add($dic['cors']);
  }
  protected function registerServices($dic)
  {
    $dic['db_url_tests']  = 'mysql://tests:tests@localhost/tests';
    $dic['db_conn_tests'] = function($dic)
    {
      return ConnectionFactory::create($dic['db_url_tests']);
    };
    $dic['user_api_controller'] = function($dic)
    {
      return new UserApiController($dic['db_conn_tests'],$dic['router']);
    };
    $dic['user_repository'] = function($dic)
    {
      return new UserRepository($dic['db_conn_tests']);
    };
    $dic['cors'] = function()
    {
      return new Cors();
    };
  }
  protected function registerRoutes($dic)
  {
    $router = $dic->get('router');
    
    $urlGenerator = function($name, $data = [], $queryParams = []) use ($router)
    {
      return $router->urlFor($name,$data,$queryParams);
    };
    
    $router->map(['GET'],'/',function($request,$response) use ($dic,$urlGenerator)
    {
      ob_start();
      require $request->getAttribute('template_name'); //'views/index.html.php';
      return $response->getBody()->write(ob_get_clean());
      
    })->setName('app_index')
      ->add(new RequestAttributes([
        'route_name'    => 'app_index',
        'template_name' => 'views/index.html.php'
      ])
    );
    $router->map(['GET'],'/users',function($request,$response) use ($urlGenerator)
    { 
      ob_start();
      require $request->getAttribute('template_name'); //'views/index.html.php';
      return $response->getBody()->write(ob_get_clean());
      
    })->setName('app_users')
      ->add(new RequestAttributes([
        'route_name'    => 'app_users',
        'template_name' => 'views/users.html.php'
      ])
    );
    /* =====================================================
     * API Routes
     */
    $router->map(['GET','OPTIONS'],'/api',function($request,$response) use ($dic)
    {
      $prefix = $request->getUri()->getScheme() . '://' . $request->getUri()->getAuthority();
      $router = $dic['router'];
      
      $links = [
        ['rel' => 'users', 'href' => $prefix . $router->urlFor('api_users')],
      ];
      $response->getBody()->write(json_encode($links));
      $response = $response->withHeader('Content-Type','application/json; charset=utf-8');
    })->setName('api_links');
    
    $router->map(['GET','OPTIONS'],'/api/users',function($request,$response) use ($dic)
    {
      $controller = $dic['user_api_controller'];
      return $controller->findAllAction($request,$response);
      
      $userRepo = $dic['user_repository'];
      $users  = $userRepo->findAll();
      $usersx = json_encode($users);
      
      $response->getBody()->write($usersx);
      $response = $response->withHeader('Content-Type','application/json; charset=utf-8');
      
      return $response;      
    })->setName('api_users');
    
    $router->map(['GET','OPTIONS'],'/api/users/{id}',function($request,$response) use ($dic)
    {
      $controller = $dic['user_api_controller'];
      return $controller->findOneAction($request,$response,$request->getAttribute('id'));
      
      $userRepo = $dic['user_repository'];
      $userId = $request->getAttribute('id');
      $user  = $userRepo->findOne($userId);
      $userx = json_encode($user);
      
      $response->getBody()->write($userx);
      $response = $response->withHeader('Content-Type','application/json; charset=utf-8');
      
      return $response;   
    })->setName('api_users_one');
  }
}
$app = new App();
$app->run();
