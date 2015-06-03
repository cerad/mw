<?php

namespace Cerad\Middleware;

class RequestAttributes
{
  protected $attrs;
  
  public function __construct(array $attrs)
  {
    $this->attrs = $attrs;
  }
  public function __invoke($request,$response,$next)
  {
    foreach($this->attrs as $key => $value) {
      $request = $request->withAttribute($key,$value);
    }
    return $next($request,$response);    
  }
}