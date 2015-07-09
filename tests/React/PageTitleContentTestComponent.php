<?php
namespace Cerad\Component\React;

class PageTitleContentTestComponent extends Component2
{
  public function render()
  {
    $titleComponent   = new   TitleTestComponent(['title'   => $this->props['title']]);
    $contentComponent = new ContentTestComponent(['content' => $this->props['content']]);

    $html = <<<TYPEOTHER
<html>
  <head>
    {$titleComponent->render()}
  </head>
  <body>
    {$contentComponent->render()}
  </body>
</html>
TYPEOTHER;
    return $html;
  }
}