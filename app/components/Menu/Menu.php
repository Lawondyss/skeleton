<?php
/**
 * Class Menu
 * @package Lawondyss
 * @author Ladislav Vondráček
 */

namespace Lawondyss;

use Nette\Application\UI;
use \Nette\Localization\ITranslator;

class Menu extends UI\Control
{
  private $items = array();

  private $translator;

  private $current;


  /**
   * @param \Nette\Localization\ITranslator $translator
   */
  public function setTranslator(ITranslator $translator)
  {
    $this->translator = $translator;
  }


  /**
   * @param string $current
   */
  public function setCurrent($current)
  {
    $this->current = $current;
  }


  /**
   * @param array $items
   */
  public function setItems($items)
  {
    foreach ($items as $item) {
      $menuItem = new MenuItem;

      foreach ($item as $name => $value) {
        $menuItem->{$name} = $value;
      }

      $this->addItem($menuItem);
    }
  }


  /**
   * @param MenuItem $item
   * @return self
   * @throws \InvalidArgumentException
   */
  public function addItem(MenuItem $item)
  {
    $key = \Nette\Utils\Strings::webalize($item->text);

    if (array_key_exists($key, $this->items)) {
      throw new \InvalidArgumentException('Item "' . $key . '" already exists. Change text by menu item.');
    }

    $this->items[$key] = $item;

    return $this;
  }


  public function render()
  {
    // added current to all items
    array_walk($this->items, function($item){$item->current = $this->current;});

    $this->template->items = $this->items;

    $this->template->setTranslator($this->translator);
    $this->template->setFile(__DIR__ . '/template.latte');
    $this->template->render();
  }
}


interface MenuFactory {

  /** @return \Lawondyss\Menu */
  public function create();
}