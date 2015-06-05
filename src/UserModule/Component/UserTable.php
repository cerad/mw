<?php
namespace Cerad\Module\UserModule\Component;

use Cerad\Component\React\Element;
use Cerad\Component\React\Component;

class UserTable extends Component
{
  protected $props = ['table-class' => 'data-table'];
    
  public function render(array $config = [])
  {
    $rows = [];
    $rowIndex = 0;
    foreach($this->props['users'] as $user)
    {
      $row = new UserTableRow([
        'key'      => $user['id'],
        'user'     => $user,
        'rowIndex' => ++$rowIndex
      ]);
      $rows[] = $row->render($config);
    }
    return new Element('table',['class' => $this->props['table-class']],$rows);
  }
}
