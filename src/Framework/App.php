<?php
namespace Cerad\Component\Framework;

use Psr\Http\Message\ResponseInterface      as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;

use Zend\Diactoros\Response      as Response;
use Zend\Diactoros\ServerRequest as Request;

class App
{
  protected $dic;
  
  protected $middlewareAfter  = [];
  protected $middlewareBefore = [];
  
  public function __construct()
  {
    $this->dic = $dic = new Container();
    
    $dic['router'] = function($dic)
    {
      return new Router();
    };
    $dic['request'] = $dic->factory(function($dic)
    {
      return new Request();
    });
    $dic['response'] = $dic->factory(function($dic)
    {
      return new Response();
    });
  }
  public function getContainer() { return $this->dic; }
  
  public function addMiddlewareBefore($mw)
  {
    $this->middlewareBefore[] = $mw;
  }
  public function addMiddlewareAfter($mw)
  {
    $this->middlewareAfter[] = $mw;
  }
  protected function processMiddleware($mws,$request,$response)
  {
    foreach($mws as $mw)
    {
      $mw = is_string($mw) ? $this->dic[$mw] : $mw;
      
      $results  = $mw($request,$response);
      $request  = isset($results[0]) ? $results[0] : $request;
      $response = isset($results[1]) ? $results[1] : $request;
    }
    return [$request,$response];
  }
  public function handle(RequestInterface $request)
  {
    $dic = $this->dic;
    
    $router   = $dic->get('router');
    $response = $dic->get('response');
    
    // TODO: Try catch around ll of this
    // 
    // Process app before
    $results  = $this->processMiddleware($this->middlewareBefore,$request,$response);
    $request  = $results[0];
    $response = $results[1];
    
    // Match the route
    $route = $router->dispatch('GET','/user/42');
    
    // Add in attributes
    
    // Process route before
    $results  = $this->processMiddleware($route['middlewareBefore'],$request,$response);
    $request  = $results[0];
    $response = $results[1];

    // Process route
    $callable = $route['callable'];
    $callable = is_string($callable) ? $dic[$callable] : $callable;
    
    $result = $callable($request,$response);
    $request  = isset($results[0]) ? $results[0] : $request;
    $response = isset($results[1]) ? $results[1] : $request;
    
    // Process route after
    $results  = $this->processMiddleware($route['middlewareAfter'],$request,$response);
    $request  = $results[0];
    $response = $results[1];
    
    // Process app after
    $results  = $this->processMiddleware($this->middlewareAfter,$request,$response);
    $request  = $results[0];
    $response = $results[1];

    // Done
    return $result[1];    
  }
}