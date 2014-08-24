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

    $resource = $this->getName();
    if ($resource !== 'Error' && !$this->user->isAllowed($resource)) {
      $userEmail = $this->user->identity->email;
      $role = implode('|', $this->user->getRoles());
      $msg = sprintf('Uživatel "%s" v roli "%s" nemá přístup ke zdroji "%s".', $userEmail, $role, $resource);
      throw new \Nette\Application\BadRequestException($msg, 403);
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
    $control->setTranslator($this->translator)
      ->setCurrent($this->name . ':' . $this->action);

    $parameters = $this->context->getParameters();
    $control->setItems($parameters['menu']['admin']);

    return $control;
  }

}
