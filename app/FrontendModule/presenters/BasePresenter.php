<?php

namespace App\FrontendModule\Presenters;

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
   * @param \Lawondyss\MenuFactory $menuFactory
   * @return \Lawondyss\Menu
   */
  protected function createComponentMenu(\Lawondyss\MenuFactory $menuFactory)
  {
    $control = $menuFactory->create();
    $control->current = $this->name . ':' . $this->action;

    $menuItems = $this->context->parameters['menu'];
    $control->setItems($menuItems['frontend']);

    return $control;
  }
}
