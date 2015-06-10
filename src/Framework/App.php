<?php
namespace Cerad\Component\Framework;

use Psr\Http\Message\ResponseInterface      as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;

use Zend\Diactoros\Response      as Response;
use Zend\Diactoros\ServerRequest as Request;

class App
{
  protected $dic;
  
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
  
  public function handle(RequestInterface $request)
  {
    $router   = $this->dic->get('router');
    $response = $this->dic->get('response');
    
    $route = $router->dispatch('GET','/user/42');
    
    $result = $route['callable']($request,$response);

    return $result[1];    
  }
  
}