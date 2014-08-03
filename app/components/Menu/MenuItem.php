<?php
/**
 * Class MenuItem
 * @package Lawondyss
 * @author Ladislav Vondráček
 */

namespace Lawondyss;

class MenuItem extends \Nette\Object
{
  public $text;

  public $link;

  public $icon;

  public $main = true;

  public $current;


  /**
   * @return bool
   */
  public function isMain()
  {
    return (bool)$this->main;
  }


  /**
   * @param bool $strict
   * @return bool
   */
  public function isCurrent($strict = false)
  {
    list($linkPresenter, $linkAction) = $this->explodeLink($this->link);
    list($currPresenter, $currAction) = $this->explodeLink($this->current);

    if ($strict && $linkAction != $currAction) {
      return false;
    }

    return ($linkPresenter == $currPresenter);
  }


  /**
   * @param string $link
   * @return array
   * @throws \RuntimeException
   * @throws \InvalidArgumentException
   */
  private function explodeLink($link)
  {
    if (!isset($link) || !is_string($link)) {
      throw new \InvalidArgumentException('Invalid link. Must be string for $presenter->link().');
    }

    if (substr($link, 0, 1) === ':') {
      $link = trim($link, ':');
    }
    $linkParams = explode(':', $link);

    switch (count($linkParams)) {
      case 3:
        list($module, $presenter, $action) = $linkParams;
        break;
      case 2:
        $module = null;
        list($presenter, $action) = $linkParams;
        break;
      default:
        throw new \RuntimeException('Undefined number of link parameters.');
    }

    $presenter = $module . ':' . $presenter;

    return array($presenter, $action);
  }
}