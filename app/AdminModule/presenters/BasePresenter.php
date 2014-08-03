<?php

namespace App\AdminModule\Presenters;

abstract class BasePresenter extends \App\Presenters\BasePresenter
{
  protected function startup()
  {
    parent::startup();
  }


  protected function beforeRender()
  {
    parent::beforeRender();
  }


  /**
   * @param \MenuFactory $menuFactory
   * @return \Menu
   */
  protected function createComponentMenu(\MenuFactory $menuFactory)
  {
    $control = $menuFactory->create();
    $control->current = $this->name . ':' . $this->action;

    $menuItems = $this->context->parameters['menu'];
    $control->setItems($menuItems['admin']);

    return $control;
  }
}
