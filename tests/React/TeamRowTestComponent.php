<?php
namespace Cerad\Component\React;

class TeamRowTestComponent extends Component2
{
  public function render()
  {
    $team = $this->props['team'];

    return <<<TYPEOTHER
<tr><td>{$this->escape($team['place'])}</td><td>{$this->escape($team['country'])}</td><td>{$this->escape($team['name'])}</td></tr>
TYPEOTHER;
  }
}