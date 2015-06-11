<?php

use FastRoute\RouteCollector                as RouteCollector;
use FastRoute\RouteParser\Std               as RouteParser;
use FastRoute\Dispatcher\GroupCountBased    as RouteDispatcher;
use FastRoute\DataGenerator\GroupCountBased as RouteDataGenerator;

use Psr\Http\Message\ResponseInterface      as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;

use Zend\Diactoros\Response      as Response;
use Zend\Diactoros\ServerRequest as Request;

use Cerad\Component\Framework\Router;

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
    
    $result = $route['mws'][0]['callable']($request,$response);
    $this->assertEquals(201,$result[1]->getStatusCode());
  }
}
