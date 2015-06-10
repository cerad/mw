<?php
namespace Cerad\Component\Framework;

use FastRoute\RouteCollector                as RouteCollector;
use FastRoute\RouteParser\Std               as RouteParser;
use FastRoute\Dispatcher\GroupCountBased    as RouteDispatcher;
use FastRoute\DataGenerator\GroupCountBased as RouteDataGenerator;

class Router
{
  protected $routes = [];
  protected $routeCollector;
  protected $routeDispatcher;
  
  public function __construct()
  {
    $this->routeCollector = new RouteCollector(new RouteParser(), new RouteDataGenerator());
  }
  /**
   * 
   * @param string          $name
   * @param string|array    $methods
   * @param string          $pattern
   * @param array           $attrs
   * @param string|callable $callable
   * @return array
   */
  public function addRoute($name, $methods, $pattern, array $attrs = [], 
    $callable = null,
    $middlewareBefore = [],
    $middlewareAfter  = [])
  {
    $this->routes[$name] = $route = [
      'name'     => $name,
      'attrs'    => $attrs,
      'methods'  => $methods,
      'pattern'  => $pattern,
      'callable' => $callable,
      'middlewareAfter'  => $middlewareAfter,
      'middlewareBefore' => $middlewareBefore,
    ];
    $this->routeCollector->addRoute($methods,$pattern,$name);
    return $route;
  }
  public function dispatch($method, $uri)
  {
    if ($this->routeDispatcher === null) {
      $this->routeDispatcher = new RouteDispatcher($this->routeCollector->getData());
    }
    $routeInfo = $this->routeDispatcher->dispatch($method,$uri);
    
    switch($routeInfo[0])
    {
      case RouteDispatcher::FOUND:
        $name  = $routeInfo[1];
        $vars  = $routeInfo[2];
        $route = $this->routes[$name];
        $route['vars'] = $vars;
        return $route;
    }
    // Toss invalid route exception?
    throw new \UnexpectedValueException(sprintf('Route not found: %s %s',$method,$uri));
  }
}