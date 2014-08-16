<?php
/**
 * Class BasePresenter
 * @package App\AdminModule\Presenters
 * @author Ladislav Vondráček
 */

namespace App\AdminModule\Presenters;

abstract class BasePresenter extends \App\Presenters\BasePresenter
{
  protected function startup()
  {
    parent::startup();

    if (!$this->user->isLoggedIn()) {
      $this->flashMessage('Musíte být přihlášeni.');
      $this->redirect(':Front:Sign:in');
    }
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
    $control->setTranslator($this->translator);
    $control->setCurrent($this->name . ':' . $this->action);

    $parameters = $this->context->getParameters();
    $control->setItems($parameters['menu']['admin']);

    return $control;
  }

}
