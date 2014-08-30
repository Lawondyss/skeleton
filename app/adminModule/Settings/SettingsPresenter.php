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


  public function actionConfirm($code)
  {
    if (isset($code)) {
      if ($code == $this->user->identity->confirm) {
        $this->successMessage('Účet potvrzen. Vítejte.');
        $this->redirect('Home:');
      }
      else {
        $msg = sprintf('Kód "%s" uživatele "%s" v URL pro ověření je chybný.', $code, $this->user->identity->email);
        $e = new \ErrorException($msg);
        $this->errorMessage('Adresa pro ověření je chybná. Zadejté kód z e-mailu.', $e);
      }
    }
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


  /**
   * @param \Lawondyss\AccountFormsFactory $accountFormsFactory
   * @param \App\Model\UserService $userService
   * @return \Lawondyss\AccountForms
   */
  protected function createComponentConfirmAccountForm(\Lawondyss\AccountFormsFactory $accountFormsFactory, \App\Model\UserService $userService)
  {
    $control = $accountFormsFactory->create();
    $control->setType($control::TYPE_CONFIRM_ACCOUNT)
      ->setTranslator($this->translator)
      ->setUserService($userService)
      ->setUser($this->user);

    $control->onSuccess[] = function() {
      $this->user->identity->confirm = null;
      $this->successMessage('Účet povrzen.');
      $this->redirect('Home:default');
    };

    $control->onException[] = function($e) {
      $this->errorMessage($this->defaultErrorMessage, $e);
      $this->redirect('this');
    };

    return $control;
  }

}