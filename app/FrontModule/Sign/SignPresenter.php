<?php

namespace App\FrontModule\Presenters;

class SignPresenter extends BasePresenter
{
  public function actionOut()
  {
    $this->user->logout(true);
    $this->redirect(':Front:Home:');
  }


  /**
   * @param \Lawondyss\AccountFormsFactory $accountFormsFactory
   * @return \Lawondyss\AccountForms
   */
  protected function createComponentSignInForm(\Lawondyss\AccountFormsFactory $accountFormsFactory)
  {
    $control = $accountFormsFactory->create();
    $control->setTranslator($this->translator);
    $control->setType($control::SIGNIN);

    $control->onSuccess[] = function() {
      $this->redirect(':Admin:Home:');
    };

    return $control;
  }


  /**
   * @param \Lawondyss\AccountFormsFactory $accountFormsFactory
   * @param \App\Model\UserService $userService
   * @return \Lawondyss\AccountForms
   */
  protected function createComponentRegisterForm(\Lawondyss\AccountFormsFactory $accountFormsFactory, \App\Model\UserService $userService)
  {
    $control = $accountFormsFactory->create();
    $control->setTranslator($this->translator);
    $control->setType($control::REGISTER);
    $control->setUserService($userService);

    $control->onException[] = function($e, $form) {
      $this->errorMessage('Něco je špatně. Zkuste to později, snad už to bude lepší.', $e);
    };

    return $control;
  }
}
