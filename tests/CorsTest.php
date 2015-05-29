<?php
//namespace Cerad\Component\Cors;

use Slim\Http\Uri;
use Slim\Http\Body;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;

use Cerad\Component\Cors\Cors;

class CorsTest extends \PHPUnit_Framework_TestCase
{
  protected function next()
  {
    return function($request,$response)
    {
      return $response;
    };
  }
  protected function createRequest($method,$headers = [])
  {
    $uri = Uri::createFromString('/users/42');
    $body = new Body(fopen('php://temp', 'r+'));
    $headers = new Headers($headers);
    return new Request($method,$uri,$headers,[],[],$body);
  }
  public function testPreflight()
  {
    $request = $this->createRequest('OPTIONS',
    [
      'Origin' => 'localhost',
      'Access-Control-Request-Method' => 'GET',
    ]);
    $response = new Response();
    
    $cors = new Cors();
    
    $response = $cors($request,$response,$this->next());
    
    $this->assertEquals('localhost',$response->getHeaderLine('Access-Control-Allow-Origin'));
  }
  public function testGet()
  {
    $request  = $this->createRequest('GET', ['Origin' => 'localhost']);
    
    $response = new Response();
    
    $cors = new Cors();
    
    $response = $cors($request,$response,$this->next());
    
    $this->assertEquals('localhost',$response->getHeaderLine('Access-Control-Allow-Origin'));
  }
}