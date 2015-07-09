<?php
namespace Cerad\Component\React;

abstract class Component2
{
  protected $props = [];
  
  public function __construct(array $props = [])
  {
    $this->props = array_replace($this->props,$props);
  }
  protected function escape($string)
  {
    return htmlspecialchars($string, ENT_COMPAT | ENT_HTML5, 'UTF-8');
  }
  public function replaceProps(array $props = [])
  {
    $this->props = array_replace($this->props,$props);
  }
  // returns one element object
  abstract public function render();

}