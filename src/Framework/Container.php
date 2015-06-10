<?php
namespace Cerad\Component\Framework;

use Interop\Container\ContainerInterface as InteropContainerInterface;
use Pimple\Container as PimpleContainer;

class Container extends PimpleContainer implements InteropContainerInterface
{
  /**
   * {@inheritdoc}
   * 
   * TODO: Add interop exception
   */
  public function get($id)
  {
    return $this->offsetGet($id);
  }
  public function has($id)
  {
    return $this->offsetExists($id);
  }
}