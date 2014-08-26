<?php
/**
 * Class Paginator
 * @package Lawondyss
 * @author Ladislav Vondráček
 */

namespace Lawondyss;

class Paginator extends \Nette\Application\UI\Control
{
  /** @persistent */
  public $page = 1;

  /** @var \Nette\Utils\Paginator */
  private $paginator;


  /**
   * @param \Nette\Utils\Paginator $paginator
   */
  public function __construct(\Nette\Utils\Paginator $paginator)
  {
    $this->paginator = $paginator;
  }


  /**
   * @param int $itemsCount
   * @return self
   */
  public function setItemsCount($itemsCount)
  {
    $this->paginator->itemCount = $itemsCount;
    return $this;
  }


  /**
   * @param int $itemsPerPage
   * @return self
   */
  public function setItemsPerPage($itemsPerPage)
  {
    $this->paginator->itemsPerPage = $itemsPerPage;
    return $this;
  }


  /**
   * @return int
   */
  public function getItemsPerPage()
  {
    return $this->paginator->itemsPerPage;
  }


  /**
   * @return int
   */
  public function getOffset()
  {
    return $this->paginator->offset;
  }


  public function render()
  {
    $page = $this->paginator->page;

    if ($this->paginator->pageCount < 2) {
      $steps = [$page];
    }
    else {
      $arr = range(max($this->paginator->firstPage, $page - 3), min($this->paginator->lastPage, $page + 3));
      $count = 4;
      $quotient = ($this->paginator->pageCount - 1) / $count;
      for ($i = 0; $i <= $count; $i++) {
        $arr[] = (int)round($quotient * $i) + $this->paginator->firstPage;
      }
      sort($arr);
      $steps = array_values(array_unique($arr));
    }

    $this->template->steps = $steps;
    $this->template->paginator = $this->paginator;

    $this->template->setFile(__DIR__ . '/template.latte');
    $this->template->render();
  }


  /**
   * @param array $params
   */
  public function loadState(array $params)
  {
    parent::loadState($params);
    $this->paginator->page = $this->page;
  }
}

interface PaginatorFactory
{
  /** @return \Lawondyss\Paginator */
  public function create();
}