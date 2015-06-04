<?php
namespace Cerad\Module\UserModule;

use Doctrine\DBAL\Connection as DbConn;

class UserApiController
{
  private $dbConn;
  private $router;
  
  public function __construct(DbConn $dbConn, $router)
  {
    $this->dbConn = $dbConn;
    $this->router = $router;
  }
  protected function getUserColumns()
  {
    return [
      'user.id        AS id',
      'user.user_name AS userName',
      'user.disp_name AS dispName',
      'user.email     AS email',
    ];
  }
  public function findAllAction($request,$response)
  {
    $qb = $this->dbConn->createQueryBuilder();
    $qb->select($this->getUserColumns());
    $qb->from  ('users','user');
    $items = $qb->execute()->fetchAll();
    
    $prefix = $request->getUri()->getScheme() . '://' . $request->getUri()->getAuthority() . '/';
    foreach($items as &$item)
    {
      $item['links'][] = [
        'rel'  => 'self',
        'href' => $prefix . $this->router->urlFor('api_users_one',['id' => $item['id']])
      ];
    }
    
    $response->getBody()->write(json_encode($items));
    $response = $response->withHeader('Content-Type','application/json;charset=utf-8');
    return $response;
  }
  public function findOneAction($request,$response,$userId)
  {
    $qb = $this->dbConn->createQueryBuilder();
    
    $qb->select($this->getUserColumns());
    $qb->from  ('users','user');
    $qb->where ('user.id = ' . $qb->createPositionalParameter($userId));
    
    $items = $qb->execute([$userId])->fetchAll();
    $item = $items[0];
    
    $item['links'][] = [
      'rel'  => 'self',
      'href' => $this->router->urlFor('api_users_one',['id' => $item['id']])
    ];
    $response->getBody()->write(json_encode($item));
    $response = $response->withHeader('Content-Type','application/json;charset=utf-8');
    return $response;
  }
}
