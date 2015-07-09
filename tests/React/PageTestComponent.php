<?php
namespace Cerad\Component\React;

class PageTestComponent extends Component2
{
  public function render()
  {
    $titleComponent = new TitleTestComponent(['title' => $this->props['title']]);

    $html = <<<TYPEOTHER
<html>
  <head>
    {$titleComponent->render()}
  </head>
</html>
TYPEOTHER;

    return $html;
  }
}