<?php
namespace Cerad\Component\React;

class TeamTableTestComponent extends Component2
{
  protected function renderRows()
  {
    $row  = new TeamRowTestComponent();
    $html = null;
    foreach($this->props['teams'] as $team)
    {
      $row->replaceProps(['team' => $team]);
      $html .= $row->render() . "\n";
    }
    return $html;
  }
  public function render()
  {
    return <<<TYPEOTHER
<table>
  <thead>
    <tr><th>Place</th><th>Country</th><th>Name</th></tr>
  </thead>
  <tbody>
{$this->renderRows()}
  </tbody>
</table>
TYPEOTHER;
  }
}