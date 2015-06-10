<?php

use FastRoute\RouteCollector                as RouteCollector;
use FastRoute\RouteParser\Std               as RouteParser;
use FastRoute\Dispatcher\GroupCountBased    as RouteDispatcher;
use FastRoute\DataGenerator\GroupCountBased as RouteDataGenerator;

use Psr\Http\Message\ResponseInterface      as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;

use Zend\Diactoros\Response      as Response;
use Zend\Diactoros\ServerRequest as Request;

use Cerad\Component\Framework\App;
use Cerad\Component\Framework\Router;
use Cerad\Component\Framework\Container;

class RouterTest extends \PHPUnit_Framework_TestCase
{
  public function testFastRoute()
  {
    $routeCollector = new RouteCollector(new RouteParser(), new RouteDataGenerator());
    
    $routeCollector->addRoute('GET', '/user/{name}/{id:[0-9]+}', 'user_name_id');
    $routeCollector->addRoute('GET', '/user/{id:[0-9]+}',        'user_id');
    $routeCollector->addRoute('GET', '/user/{name}',             'user_name');

    // getData is an array or parsed route information
    $routeDispatcher = new RouteDispatcher($routeCollector->getData());
   
    $routeInfo = $routeDispatcher->dispatch('GET', '/user/42');
    $this->assertEquals(RouteDispatcher::FOUND,$routeInfo[0]);
    $this->assertEquals('user_id',$routeInfo[1]);
    $this->assertEquals(42,       $routeInfo[2]['id']);
    
    $routeInfo = $routeDispatcher->dispatch('GET', '/user/art/42');
    $this->assertEquals(RouteDispatcher::FOUND,$routeInfo[0]);
    $this->assertEquals('user_name_id',$routeInfo[1]);
    $this->assertEquals(42,            $routeInfo[2]['id']);
    $this->assertEquals('art',         $routeInfo[2]['name']);
  }
  public function testRouter()
  {
    $router = new Router();
    $router->addRoute('user_name_id', 'GET', '/user/{name}/{id:[0-9]+}');
    $router->addRoute('user_id',      'GET', '/user/{id:[0-9]+}',['model' => 'user']);
    $router->addRoute('user_name',    'GET', '/user/{name}');
    
    $route = $router->dispatch('GET','/user/42');
    $this->assertEquals(42,$route['vars']['id']);
    $this->assertEquals('user',$route['attrs']['model']);
  }
  public function testCallable()
  {
    $callable = function(RequestInterface $request, ResponseInterface $response)
    {
      return [$request, $response->withStatus(201)];
    };
    $router = new Router();
    $router->addRoute('user_id','GET', '/user/{id:[0-9]+}',['model' => 'user'],$callable);
    $route = $router->dispatch('GET','/user/42');
    
    $request  = new Request();
    $response = new Response();
    
    $result = $route['callable']($request,$response);
    $this->assertEquals(201,$result[1]->getStatusCode());
  }
  public function testContainer()
  {
    $dic = new Container();
    
    $this->assertFalse($dic->has('router'));
    
    $dic['router'] = function($dic)
    {
      return new Router();
    };
    $router = $dic->get('router');
    
    $this->assertTrue($router instanceof Router);
    
  //$this->assertInstanceOf('Router',$router);
    
  }
  public function testApp()
  {
    $app = new App();
    $dic = $app->getContainer();
    
    $router = $dic->get('router');
    $this->assertTrue($router instanceof Router);
    
    $this->assertTrue($dic->get('request')  instanceof RequestInterface);
    $this->assertTrue($dic->get('response') instanceof ResponseInterface);
  }
  public function testAppHandle()
  {
    $app = new App();
    $dic = $app->getContainer();
    
    $router = $dic->get('router');
    
    $callable = function(RequestInterface $request, ResponseInterface $response)
    {
      return [$request,$response->withStatus(201)];
    };
    $router->addRoute('user_id','GET', '/user/{id:[0-9]+}',['model' => 'user'],$callable);

    $request = new Request();
    
    $response = $app->handle($request);
    $this->assertEquals(201,$response->getStatusCode());
  }
}
