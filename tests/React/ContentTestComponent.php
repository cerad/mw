<?php
namespace Cerad\Component\React;

class ContentTestComponent extends Component2
{
  public function render()
  {
    $html = <<<TYPEOTHER
<div id="content>
{$this->escape($this->props['content'])}
</div>
TYPEOTHER;
    return $html;
  }
}