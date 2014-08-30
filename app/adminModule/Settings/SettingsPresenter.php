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
    $control->setType($control::TYPE_CHANGE_PASSWORD)
      ->setTranslator($this->translator)
      ->setUserService($userService)
      ->setUser($this->user);

    $control->onSuccess[] = function() {
      $this->successMessage('Heslo bylo změněno.');
      $this->redirect('this');
    };

    $control->onException[] = function($e) {
      $this->errorMessage('Něco je špatně. Zkuste to později, snad to bude lepší.', $e);
    };

    return $control;
  }


  /**
   * @param \Lawondyss\AccountFormsFactory $accountFormsFactory
   * @param \App\Model\UserService $userService
   * @return \Lawondyss\AccountForms
   */
  protected function createComponentDeleteAccountForm(\Lawondyss\AccountFormsFactory $accountFormsFactory, \App\Model\UserService $userService)
  {
    $control = $accountFormsFactory->create();
    $control->setType($control::TYPE_VERIFY_PASSWORD)
      ->setTranslator($this->translator)
      ->setUserService($userService)
      ->setUser($this->user);

    $control->onSuccess[] = function() use ($userService) {
      $userService->delete($this->user->id);
      $this->successMessage('Váš účet byl smazán.');
      $this->redirect(':Front:Home:');
    };

    $control->onException[] = function($e) {
      $this->errorMessage('Něco je špatně. Zkuste to později, snad to bude lepší.', $e);
    };

    return $control;
  }

}