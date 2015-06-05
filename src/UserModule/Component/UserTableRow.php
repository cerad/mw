<?php
namespace Cerad\Module\UserModule\Component;

use Cerad\Component\React\Element;
use Cerad\Component\React\Component;

class UserTableRow extends Component
{
  public function render(array $config = [])
  {
    $trProps = [ 'class' => 'odd' ];
      
    if (($this->props['rowIndex'] % 2) === 0) {
      $trProps['class'] = 'even';
    }
    
    $user = $this->props['user'];
    
    return new Element('tr',$trProps,[
      new Element('td',null,$user['id']),
      new Element('td',null,$user['userName']),      
      new Element('td',null,$user['dispName']),
      new Element('td',null,$user['email']),      
    ]);
  }
}
