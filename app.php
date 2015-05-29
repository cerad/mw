<?php
require 'vendor/autoload.php';

use Slim\App;

echo sprintf("Starting app\n\n");

$app = new App();
$container = $app->getContainer();
$container['data'] = 'Data';

$mw1 = function ($request, $response, $next) {
  $response->write("MW1 BEFORE\n");
  $response = $next($request, $response);
  $response->write("MW1 AFTER\n");
  return $response;
};
$mwRouteIndex = function ($request, $response, $next) {
  
  $response->write("MW2 BEFORE\n");
  $response = $next($request, $response);
  $response->write("MW2 AFTER\n");
  return $response;
};
$mwRouteHello = function ($request, $response, $next) {
  
  $request = $request->withAttribute('_role','admin');
  
  $response->write("MW Hello\n");
  $response = $next($request, $response);
  return $response;
};
$mwAuthorize = function ($request, $response, $next) {
  
  $queryParams = $request->getQueryParams();
  $userRole = isset($queryParams['role']) ? $queryParams['role'] : null;
  
  $authRole = $request->getAttribute('_role');
  
  $response->write(sprintf("Authorize %s %s %s\n",$authRole,$userRole,$this['data']));
  $response = $next($request, $response);
  return $response;
};
$this['cors'] = function ($request, $response, $next) {

    if ($request->isOptions())
    {
      $response->write("CORS Preflight\n");
      return $response->withHeader('CORS','something');
    }
    return $next($request,$response);
};
$app->add($mw1);
$route = $app->get('/', function ($req, $res, $args) {
    echo " Route Index\n";
})->add($mwRouteIndex);

$route = $app->any('/hello/{name}', function ($req, $res, $args) {
    echo sprintf(" Route Hello %s %s\n",$args['name'],$req->getAttribute('_role'));
})->add($mwAuthorize)
  ->add($this['cors'])
  ->add($mwRouteHello);
//echo sprintf("Route class: %s\n",get_class($route));

$_SERVER['REQUEST_METHOD'] = 'GET'; //OPTIONS';
$_SERVER['REQUEST_URI']    = '/hello/art';
$_SERVER['QUERY_STRING']   = 'role=adminx';

$app->run();

echo sprintf("Exiting app\n\n");
