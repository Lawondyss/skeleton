<?php

namespace App\FrontModule\Presenters;

class SignPresenter extends BasePresenter
{
  public function actionOut()
  {
    $this->user->logout(true);
    $this->redirect(':Front:Home:');
  }


  public function renderReset()
  {
    $token = $this->presenter->getParameter('token');
    if (!isset($token) || $token === '') {
      $this->errorMessage('Nelze najít ověřovací token. Pokud jste neupravili odkaz, je to naše vina.');
    }
  }


  /**
   * @param \Lawondyss\AccountFormsFactory $accountFormsFactory
   * @return \Lawondyss\AccountForms
   */
  protected function createComponentSignInForm(\Lawondyss\AccountFormsFactory $accountFormsFactory)
  {
    $control = $accountFormsFactory->create();
    $control->setTranslator($this->translator)
      ->setType($control::SIGNIN);

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
    $control->setTranslator($this->translator)
      ->setType($control::REGISTER)
      ->setUserService($userService);

    $control->onException[] = function($e, $form) {
      $this->errorMessage('Něco je špatně. Zkuste to později, snad už to bude lepší.', $e);
    };

    return $control;
  }


  /**
   * @param \Lawondyss\AccountFormsFactory $accountFormsFactory
   * @param \App\Model\UserService $userService
   * @param \Nette\Mail\IMailer $mailer
   * @return \Lawondyss\AccountForms
   */
  protected function createComponentForgetForm(\Lawondyss\AccountFormsFactory $accountFormsFactory, \App\Model\UserService $userService, \Nette\Mail\IMailer $mailer)
  {
    $control = $accountFormsFactory->create();
    $control->setMailer($mailer)
      ->setTranslator($this->translator)
      ->setType($control::FORGET)
      ->setEmailFrom($this->getAppParameter('email.noreply'))
      ->setUserService($userService);

    $control->onException[] = function($e) {
      $this->errorMessage('Něco je špatně. Zkuste to později, snad už to bude lepší.', $e);
    };

    $control->onSuccess[] = function() {
      $this->successMessage('Byl odeslán resetovací e-mail.');
      $this->redirect('this');
    };

    return $control;
  }


  /**
   * @param \Lawondyss\AccountFormsFactory $accountFormsFactory
   * @param \App\Model\UserService $userService
   * @return \Lawondyss\AccountForms
   */
  protected function createComponentResetForm(\Lawondyss\AccountFormsFactory $accountFormsFactory, \App\Model\UserService $userService)
  {
    $control = $accountFormsFactory->create();
    $control->setTranslator($this->translator)
      ->setType($control::RESET)
      ->setUserService($userService);

    $control->onException[] = function($e) {
      $this->errorMessage('Něco je špatně. Zkuste to později, snad už to bude lepší.', $e);
      $this->redirect('Sign:forget');
    };

    $control->onSuccess[] = function() {
      $this->successMessage('Heslo bylo uloženo. Nyní se můžete přihlásit.');
      $this->redirect('Sign:in');
    };

    return $control;
  }
}
