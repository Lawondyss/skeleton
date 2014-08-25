<?php
/**
 * Class SettingsPresenter
 * @package App\AdminModule\Presenters
 * @author Ladislav Vondráček
 */

namespace App\AdminModule\Presenters;

class SettingsPresenter extends BasePresenter
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
   * @param \Lawondyss\AccountFormsFactory $accountFormsFactory
   * @param \App\Model\UserService $userService
   * @return \Lawondyss\AccountForms
   */
  protected function createComponentChangePasswordForm(\Lawondyss\AccountFormsFactory $accountFormsFactory, \App\Model\UserService $userService)
  {
    $control = $accountFormsFactory->create();
    $control->setTranslator($this->translator)
      ->setUserService($userService)
      ->setUser($this->user)
      ->setType($control::CHANGE);

    $control->onSuccess[] = function() {
      $this->successMessage('Heslo bylo změněno.');
      $this->redirect('this');
    };

    $control->onException[] = function($e) {
      $this->errorMessage('Něco je špatně. Zkuste to později, snad to bude lepší.', $e);
    };

    return $control;
  }

}