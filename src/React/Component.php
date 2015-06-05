<?php
namespace Cerad\Component\React;

abstract class Component
{
  protected $props = [];
  
  public function __construct(array $props = [])
  {
    $this->props = array_replace($this->props,$props);
  }
  // returns one element object
  abstract public function render(array $config = []);
  
  // Returns html string
  public function renderToString(array $config = [])
  {
    $element = $this->render($config);
    
    return $element->render($config);
  }
  // Expensive but very handy for testing
  public function renderToStringPretty(array $config = [])
  {
    $html = $this->renderToString($config);
    
    $dom = new \DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->loadXML($html);
    $dom->formatOutput = true;
    
    // Cannnot do this because it changes <table></table> to <table/>
    echo $dom->saveHTML(); die();
    return substr($dom->saveXML(),22); // Strip <_xml version="1.0_>" 
  }
  
  /* ========================================
   * Doubt if need these but oh well
   * Want to keep default properties?
   */
  public function mergeProps(array $props)
  {
    $this->props = array_replace($this->props,$props);
  }
  public function replaceProps(array $props)
  {
    $this->props = $props;
  }
}