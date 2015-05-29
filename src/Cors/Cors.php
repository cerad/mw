<?php

namespace Cerad\Component\Cors;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Slim\Http\Headers;
use Slim\Http\Response;

/* ========================================================
 * http://www.html5rocks.com/en/tutorials/cors/
 */
class Cors
{
  public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next = null)
  {
    if (
      $request->hasHeader('Origin') && 
      $request->hasHeader('Access-Control-Request-Method') &&
      $request->isOptions()) {
      
      $allowOrigin  = $request->getHeaderLine('Origin');
      $allowHeaders = $request->getHeaderLine('Access-Control-Request-Headers');
      
      $allowMethods = 'GET,POST,PUT,PATCH,DELETE'; // For caching
      $allowHeaders = $allowHeaders ? $allowHeaders : 'Content-Type, Accept';
    
      $headers = new Headers([  
        'Access-Control-Allow-Origin'  => $allowOrigin,
        'Access-Control-Allow-Methods' => $allowMethods,
        'Access-Control-Allow-Headers' => $allowHeaders,
        'Access-Control-Max-Age'       => 100,
      ]);
      return new Response(200,$headers);
    }
    $response = $next($request,$response);
    
    // Need origin for CORS
    $origin = $request->getHeaderLine('Origin');
    if (!$origin) return $response;
    
    // Set the header
    return $response->withHeader('Access-Control-Allow-Origin',$origin);
  }
}