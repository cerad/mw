<?php
namespace Cerad\Component\React;

class ReactTest extends \PHPUnit_Framework_TestCase
{
  protected function escape($str)
  {
    return $str;
  }
  public function testHereDoc()
  {
    $props = [
      'title' => 'Hi'
    ];

    $html = <<<EOT
<h1>{$this->escape($props['title'])}</h1>
EOT;
    $this->assertEquals('<h1>Hi</h1>',$html);
  }
  public function testTitleComponent()
  {
    $title = new TitleTestComponent(['title' => 'Buffy & Spike']);
    $this->assertEquals('<title>Buffy &amp; Spike</title>',$title->render());
  }
  public function testPageComponent()
  {
    $page = new PageTestComponent(['title' => 'Buffy & Spike']);

    $expect = <<<TYPEOTHER
<html>
  <head>
    <title>Buffy &amp; Spike</title>
  </head>
</html>
TYPEOTHER;

    $this->assertEquals($expect,$page->render());
  }
  public function testPageTitleContentComponent()
  {
    $page = new PageTitleContentTestComponent([
      'title'   => 'Buffy & Spike',
      'content' => 'A Love Story'
    ]);

    $expect = <<<TYPEOTHER
<html>
  <head>
    <title>Buffy &amp; Spike</title>
  </head>
  <body>
    <div id="content>
A Love Story
</div>
  </body>
</html>
TYPEOTHER;
    $this->assertEquals($expect,$page->render());
  }
  public function testTeamRowComponent()
  {
    $team = ['place' => '1st', 'country' => 'USA', 'name' => 'United States'];

    $teamRow = new TeamRowTestComponent(['team' => $team]);

    $expect = <<<TYPEOTHER
<tr><td>1st</td><td>USA</td><td>United States</td></tr>
TYPEOTHER;
    $this->assertEquals($expect,$teamRow->render());
  }
  public function testTeamTableComponent()
  {
    $teams = [
      ['place' => '1st', 'country' => 'USA', 'name' => 'United States'],
      ['place' => '2nd', 'country' => 'JAP', 'name' => 'Japan'],
      ['place' => '4th', 'country' => 'GER', 'name' => 'Germany'],
      ['place' => '3rd', 'country' => 'END', 'name' => 'England'],
    ];
    $teamTable = new TeamTableTestComponent(['teams' => $teams]);

    $expect = <<<TYPEOTHER
<table>
  <thead>
    <tr><th>Place</th><th>Country</th><th>Name</th></tr>
  </thead>
  <tbody>
<tr><td>1st</td><td>USA</td><td>United States</td></tr>
<tr><td>2nd</td><td>JAP</td><td>Japan</td></tr>
<tr><td>4th</td><td>GER</td><td>Germany</td></tr>
<tr><td>3rd</td><td>END</td><td>England</td></tr>

  </tbody>
</table>
TYPEOTHER;
    // Need to figure out this line ending stuff some day
    $expect = str_replace(["\n","\r"],'',$expect);
    $html   = str_replace(["\n","\r"],'',$teamTable->render());
    $this->assertEquals($expect,$html);
  }

}