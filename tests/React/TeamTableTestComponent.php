<?php
namespace Cerad\Component\React;

class TeamTableTestComponent extends Component2
{
  protected function renderRows($teams)
  {
    $row  = new TeamRowTestComponent();
    $html = null;
    foreach($teams as $team) {
      $row->replaceProps(['team' => $team]);
      $html .= $row->render() . "\n";
    }
    return $html;
  }
  public function render()
  {
    $teams = $this->props['teams'];
    usort($teams,function($team1,$team2) {
      if ($team1['place'] < $team2['place']) return -1;
      if ($team1['place'] > $team2['place']) return  1;
      return 0;
    });
    return <<<TYPEOTHER
<table>
  <thead>
    <tr><th colspan="5">{$this->escape($this->props['title'])}</th></tr>
    <tr><th>Place</th><th>Country</th><th>Name</th></tr>
  </thead>
  <tbody>
{$this->renderRows($teams)}
  </tbody>
</table>
TYPEOTHER;
  }
}