<?php

use Cerad\Component\React\Element;
use Cerad\Component\React\Component;

class ReactTest extends \PHPUnit_Framework_TestCase
{
  public function testH1()
  {
    $h1 = new Element('h1',['class' => 'bold'],'H1 <> Test');
    $html = $h1->render();
    $htmlx = '<h1 class="bold">H1 &lt;&gt; Test</h1>';
    $this->assertEquals($htmlx,$html);
  }
  public function testDivSpan()
  {
    $span = new Element('span',null,'Spanned Text');
    $div = new Element('div',['id' => 'user-table'],[$span]);
    $html = $div->render();
    $this->assertEquals('<div id="user-table"><span>Spanned Text</span></div>',$html);
  }
  public function testComponentRow()
  {
    $row = new TestComponentRow(['user' => 
      ['user' => 'ahundiak', 'name' => 'Art H']
    ]);
    $element = $row->render();
    $this->assertEquals('Cerad\Component\React\Element',get_class($element));
    
    $htmlx = '<tr><td>ahundiak</td><td>Art H</td></tr>';
    $this->assertEquals($htmlx,$element->render());
  }
  public function testComponentTable()
  {
    $table = new TestComponentTable([ 'users' => [
      ['user' => 'user1', 'name' => 'name1'],
      ['user' => 'user2', 'name' => 'name2'],
    ]]);
    $element = $table->render();
    
    $htmlx = 
      '<table class="data-table">' . 
        '<tr><td>user1</td><td>name1</td></tr>' . 
        '<tr><td>user2</td><td>name2</td></tr>' . 
      '</table>';
    $this->assertEquals($htmlx,$table->render()->render());
    $this->assertEquals($htmlx,$table->renderToString());
    
    //echo "\n" . $table->renderToStringPretty();
    
    $htmlPrettyx = <<<EOT
<table class="data-table">
  <tr>
    <td>user1</td>
    <td>name1</td>
  </tr>
  <tr>
    <td>user2</td>
    <td>name2</td>
  </tr>
</table>

EOT;
    // The EOT inserts \r as well as \n
    $htmlPretty  = str_replace([],    '',$table->renderToStringPretty());
    $htmlPrettyx = str_replace(["\r"],'',$htmlPrettyx);
    $this->assertEquals($htmlPrettyx,$htmlPretty);
  }
}
class TestComponentRow extends Component
{
  public function render(array $config = [])
  {
    $user = $this->props['user'];
    return new Element('tr',null,[
      new Element('td',null,$user['user']),
      new Element('td',null,$user['name']),      
    ]);
  }
}
class TestComponentTable extends Component
{
  protected $props = ['table-class' => 'data-table'];
    
  public function render(array $config = [])
  {
    $rows = [];
    foreach($this->props['users'] as $user)
    {
      $row = new TestComponentRow(['user' => $user]);
      $rows[] = $row->render($config);
    }
    return new Element('table',['class' => $this->props['table-class']],$rows);
  }
}