<?php
namespace Cerad\Component\React;

class TitleTestComponent extends Component2
{
  public function render()
  {
    return <<<TYPEOTHER
<title>{$this->escape($this->props['title'])}</title>
TYPEOTHER;
  }
}