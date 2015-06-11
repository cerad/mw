<?php

use Psr\Http\Message\ResponseInterface      as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;

//  Zend\Diactoros\Response      as Response;
use Zend\Diactoros\ServerRequest as Request;

use Cerad\Component\Framework\App;
use Cerad\Component\Framework\Router;

class AppTest extends \PHPUnit_Framework_TestCase
{
  public function testConstructor()
  {
    $app = new App();
    $dic = $app->getContainer();
    
    $router = $dic->get('router');
    $this->assertTrue($router instanceof Router);
    
    $this->assertTrue($dic->get('request')  instanceof RequestInterface);
    $this->assertTrue($dic->get('response') instanceof ResponseInterface);
  }
  public function testHandle()
  {
    $app = new App();
    $dic = $app->getContainer();
    
    $router = $dic->get('router');
    
    $callable = function(RequestInterface $request, ResponseInterface $response)
    {
      $response = $response->withStatus(201);
      
      // Hack to verify request attributes are being set
      $data = [
        'routeName' => $request->getAttribute('_route'),
        'model'     => $request->getAttribute('model')
      ];
      $response->getBody()->write(json_encode($data));
      
      return [$request,$response];
    };
    $router->addRoute('user_id','GET', '/user/{id:[0-9]+}',['model' => 'user'],$callable);

    $request = new Request([],[],'/user/42','GET');
    
    $response = $app->handle($request);
    $this->assertEquals(201,   $response->getStatusCode());
    
    $body = $response->getBody(); $body->rewind();
    $data = json_decode($body->getContents(),true);
    
    $this->assertEquals('user_id',$data['routeName']);
    $this->assertEquals('user',   $data['model']);
  }
  public function testHandleStringCallable()
  {
    $app = new App();
    $dic = $app->getContainer();
    
    $router = $dic->get('router');
    
    $callable = function(RequestInterface $request, ResponseInterface $response)
    {
      return [$request,$response->withStatus(201)];
    };
    $dic['user_id_callable'] = $dic->protect($callable);
    
    $router->addRoute('user_id','GET', '/user/{id:[0-9]+}',['model' => 'user'],'user_id_callable');

    $request = new Request([],[],'/user/42','GET');
    
    $response = $app->handle($request);
    $this->assertEquals(201,$response->getStatusCode());
  }
  public function testHandleAppMiddleware()
  {
    $mwa1 = function(RequestInterface $request, ResponseInterface $response)
    {
      echo "mwa1\n";
      return [$request,$response];
    };
    $mwa2 = function(RequestInterface $request, ResponseInterface $response)
    {
      echo "mwa2\n";
      return [$request,$response];
    };
    $mwr1 = function(RequestInterface $request, ResponseInterface $response)
    {
      echo "mwr1\n";
      return [$request,$response];
    };
    $mwr2 = function(RequestInterface $request, ResponseInterface $response)
    {
      echo "mwr2\n";
      return [$request,$response];
    };
    $callable = function(RequestInterface $request, ResponseInterface $response)
    {
      echo "callable\n";
      return [$request,$response->withStatus(201)];
    };
    
    $app = new App();
    $dic = $app->getContainer();
    
    $app->addMiddleware( 255,$mwa1);
    $app->addMiddleware(-255,$mwa2);
    
    $mws = [
      ['priority' =>  255, 'callable' => $mwr1],
      ['priority' =>    0, 'callable' => $callable],
      ['priority' => -255, 'callable' => $mwr2],
    ];
    $router = $dic->get('router');
    $router->addRoute('user_id','GET', '/user/{id:[0-9]+}',['model' => 'user'],$mws);
    
    $request = new Request([],[],'/user/42','GET');
   
    $response = $app->handle($request);
   
    // Just cant seem to get heredoc to work
    $output = <<<EOT
mwa1
mwr1
callable
mwr2
mwa2
EOT;
    $output = str_replace("\r",'',$output);
    $output = str_replace("\n",PHP_EOL,$output);

    $output = "mwa1\n" . "mwr1\n" . "callable\n" . "mwr2\n" . "mwa2\n";
    $this->expectOutputString($output);
  }
}
