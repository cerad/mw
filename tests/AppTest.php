<?php

use Slim\App;

use Cerad\Component\Cors\Cors;

class AppTest extends \PHPUnit_Framework_TestCase
{
  public function testNotFound()
  {
    $_SERVER['REQUEST_METHOD'] = 'GET'; //OPTIONS';
    $_SERVER['REQUEST_URI']    = '/hello/art';
    $_SERVER['QUERY_STRING']   = 'role=adminx';

    $app = new App();
    $container = $app->getContainer();
    
    $request  = $container->get('request');
    $response = $container->get('response');
    
    $response = $app->callMiddlewareStack($request,$response);
    $this->assertEquals(404,$response->getStatusCode());
  }
  public function testGet()
  {
    $_SERVER['REQUEST_METHOD'] = 'GET'; //OPTIONS';
    $_SERVER['REQUEST_URI']    = '/hello/art';
    $_SERVER['QUERY_STRING']   = 'role=adminx';

    $app = new App();
    $container = $app->getContainer();
    
    $app->get('/hello/{name}', function ($request, $response, $args) {
      $response->write(sprintf("HELLO %s",$args['name']));
      return $response;
    });
    
    $request  = $container->get('request');
    $response = $container->get('response');
    
    $response = $app->callMiddlewareStack($request,$response);
    $this->assertEquals(200,$response->getStatusCode());
    
    $body = $response->getBody();
    $body->rewind();
    $this->assertEquals('HELLO art',$body->getContents());
  }
  public function testContainer()
  {
    $_SERVER['REQUEST_METHOD'] = 'GET'; //OPTIONS';
    $_SERVER['REQUEST_URI']    = '/hello/art';
    $_SERVER['QUERY_STRING']   = 'role=adminx';

    $app = new App();
    $container = $app->getContainer();
    $container['data'] = 'data';
    $router = $container->get('router');
    
    $router->map(['GET'],'/hello/{name}', function ($request, $response, $args) use ($container) {
      $response->write(sprintf("HELLO %s %s",$args['name'],$container['data']));
      return $response;
    });
    
    $request  = $container->get('request');
    $response = $container->get('response');
    
    $response = $app->callMiddlewareStack($request,$response);
    $this->assertEquals(200,$response->getStatusCode());
    
    $body = $response->getBody();
    $body->rewind();
    $this->assertEquals('HELLO art data',$body->getContents());
  }
  public function testCorsPreflight()
  {
    $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
    $_SERVER['REQUEST_URI']    = '/hello/art';
    $_SERVER['QUERY_STRING']   = 'role=adminx';
    
    $_SERVER['HTTP_ORIGIN'] = 'localhost';
    $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] = 'GET';
    
    $app = new App();
    
    $cors = new Cors();
    
    $app->options('/hello/{name}', function ($request, $response, $args) {
      $response->write(sprintf("HELLO %s",$args['name']));
      return $response;
    })->add($cors);
    
    $request  = $app->request; //$container->get('request');
    $response = $app->response; //$container->get('response');
    
    $this->assertTrue($request->hasHeader('Access-Control-Request-Method'));
    
    $response = $app->callMiddlewareStack($request,$response);
    $this->assertEquals(200,$response->getStatusCode());
        
    $this->assertEquals('localhost',$response->getHeaderLine('Access-Control-Allow-Origin'));
  }
  public function testCors()
  {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI']    = '/hello/art';
    $_SERVER['QUERY_STRING']   = 'role=adminx';
    
    $_SERVER['HTTP_ORIGIN'] = 'localhost';
    
    $app = new App();
    
    $cors = new Cors();
    
    $app->get('/hello/{name}', function ($request, $response, $args) {
      $response->write(sprintf("HELLO %s",$args['name']));
      return $response;
    })->add($cors);
    
    $request  = $app->request; //$container->get('request');
    $response = $app->response; //$container->get('response');
    
    $response = $app->callMiddlewareStack($request,$response);
    $this->assertEquals(200,$response->getStatusCode());
        
    $this->assertEquals('localhost',$response->getHeaderLine('Access-Control-Allow-Origin'));
  }
}
