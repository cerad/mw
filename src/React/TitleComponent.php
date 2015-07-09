<?php
namespace Cerad\Component\React;

class TitleComponent extends Component2
{
  public function render()
  {
    return sprintf('<title>%s</title>',$this->escape($this->props['title']));
  }
}