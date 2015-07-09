<?php
namespace Cerad\Component\React;

class PageComponent extends Component2
{
  public function render()
  {
    $titleComponent = new TitleComponent(['title' => $this->props['title']]);

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