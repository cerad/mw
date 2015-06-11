<?php
namespace Cerad\Component\Framework;

use Psr\Http\Message\ResponseInterface      as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;

use Zend\Diactoros\Response      as Response;
use Zend\Diactoros\ServerRequest as Request;

class App
{
  protected $dic;
  
  protected $mws = [];
  
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
  
  public function addMiddleware($priority, $callable)
  {
    $this->mws[] = ['priority' => $priority, 'callable' => $callable];
  }
  public function addMiddlewareBefore($callable, $priority = 255)
  {
    $this->mws[] = ['priority' => $priority, 'callable' => $callable];
    
    $this->middlewareBefore[] = $callable;
  }
  public function addMiddlewareAfter($callable, $priority = -255)
  {
    $this->mws[] = ['priority' => $priority, 'callable' => $callable];
    
    $this->middlewareAfter[] = $callable;
  }
  protected function processMiddleware($mws,$request,$response)
  {
    usort($mws,function($mw1,$mw2) {
      // intcmp hack
      return $mw2['priority'] - $mw1['priority'];
    });
    
    foreach($mws as $mw)
    {
      $callable = is_string($mw['callable']) ? $this->dic[$mw['callable']] : $mw['callable'];
      
      $results  = $callable($request,$response);
      $request  = isset($results[0]) ? $results[0] : $request;
      $response = isset($results[1]) ? $results[1] : $request;
    }
    return [$request,$response];
  }
  protected function processMiddlewarex($mws,$request,$response)
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
    $response = $this->dic->get('response');
    
    // TODO: Try catch around ll of this
    
    // TODO: Sort by priority?
    // $mws = array_merge($this->middlewareBefore,[$this],$this->middlewareAfter);
    $mws   = $this->mws;
    $mws[] = ['priority' => 0, 'callable' => $this];
    $results = $this->processMiddleware($mws,$request,$response);
    
    return $results[1];
  }
  /* ===============================================
   * The app is also a callable bit of middleware that handles the route
   * Might want to move to it's own class
   */
  public function __invoke(RequestInterface $request, ResponseInterface $response)
  {
    // Match the route
    $dic = $this->dic;
    $router = $this->dic->get('router');
    $route  = $router->dispatch($request->getMethod(),$request->getUri()->getPath());
    
    // Add in attributes
    $attrs = array_replace(
      $route['attrs'],
      $route['vars'],[
        '_route'     => $route['name'], // Stay compatible with S2
        '_routeInfo' => $route,
      ]
    );
    foreach ($attrs as $key => $value)
    {
      $request = $request->withAttribute($key,$value);
    }
    //$mws = array_merge($route['middlewareBefore'],[$route['callable']],$route['middlewareAfter']);
    
    return $this->processMiddleware($route['mws'],$request,$response);
    
  }
}