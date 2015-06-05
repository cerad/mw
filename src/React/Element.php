<?php
namespace Cerad\Component\React;

class Element
{
  protected $name;
  protected $attrs = [];
  protected $content;
  
  public function __construct($name,$attrs = null,$content = null)
  {
    $this->name    = $name;
    $this->attrs   = $attrs === null ? $this->attrs : array_replace($this->attrs,$attrs);
    $this->content = $content;
  }
  protected function escape($string)
  {
    return htmlspecialchars($string, ENT_COMPAT | ENT_HTML5, 'UTF-8');
  }
  // Return html string
  public function render(array $config = [])
  {
    $html = '<' . $this->name;
    foreach($this->attrs as $name => $value)
    {
      $html .= ' ' . $name . '="' . $this->escape($value) . '"';
    }
    $content = $this->content;
    if ($content === null)
    {
      switch($this->name)
      {
        case 'input':
        case 'link':
          return $html . '/>';
      }
      return $html . '></' . $this->name . '>';
    }
    if (!is_array($content))
    {
      return $html . '>' . $this->escape($content) . '</' . $this->name . '>';
    }
    $html .= '>';
    foreach($content as $child)
    {
      $html .= $child->render($config);
    }
    return $html . '</' . $this->name . '>';
  }
}