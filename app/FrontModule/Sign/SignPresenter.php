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
   */
  protected function createComponentSignInForm(\Lawondyss\AccountFormsFactory $accountFormsFactory)
  {
    $control = $accountFormsFactory->create();
    $control->setType($control::SIGNIN);

    $control->onSuccess[] = function() {
      $this->redirect(':Admin:Home:');
    };

    return $control;
  }
}
