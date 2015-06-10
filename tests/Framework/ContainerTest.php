<?php

use Cerad\Component\Framework\Container;

class Router
{
  
}
class ContainerTest extends \PHPUnit_Framework_TestCase
{
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
}
