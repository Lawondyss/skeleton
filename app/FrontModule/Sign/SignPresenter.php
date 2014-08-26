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

    // check token
    if (!isset($token) || $token === '' || !is_numeric($token)) {
      $e = new \ErrorException('Nelze najít token.');
      $this->errorMessage('Chybná adresa. Pokud jste neupravili odkaz, zkuste znova zažádat o reset hesla. Jinak použijte původní odkaz.', $e);
      $this->redirect('forget');
    }

    // check expire token
    $validity = \Security\Authenticator::validateToken($token, $this->getAppParameter('tokenExpire'));
    if (!$validity) {
      $this->errorMessage('Čas pro reset hesla vypršel. Zažádejte znova.');
      $this->redirect('forget');
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
      ->setType($control::SIGN_IN);

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

    $control->onException[] = function($e) {
      $this->errorMessage($this->defaultErrorMessage, $e);
    };

    return $control;
  }


  /**
   * @param \Lawondyss\AccountFormsFactory $accountFormsFactory
   * @param \App\Model\UserService $userService
   * @param \Lawondyss\Mails $mails
   * @return \Lawondyss\AccountForms
   */
  protected function createComponentForgetForm(\Lawondyss\AccountFormsFactory $accountFormsFactory, \App\Model\UserService $userService, \Lawondyss\Mails $mails)
  {
    $control = $accountFormsFactory->create();
    $control->setTranslator($this->translator)
      ->setType($control::FORGET)
      ->setUserService($userService);

    $control->onException[] = function($e) {
      $this->errorMessage($this->defaultErrorMessage, $e);
    };

    $control->onSuccess[] = function(\Nette\Application\UI\Form $form, $values) use ($mails) {
      $from = $this->getAppParameter('email.noreply');
      $to = $values->email;
      $token = $values->token;
      $link = $_SERVER['HTTP_HOST'] . $this->link('Sign:reset', ['token' => $token]);
      $webTitle = $this->getAppParameter('title');
      $mails->sendForgetMail($from, $to, $link, $webTitle);

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
      $this->errorMessage($this->defaultErrorMessage, $e);
      $this->redirect('Sign:forget');
    };

    $control->onSuccess[] = function() {
      $this->successMessage('Heslo bylo uloženo. Nyní se můžete přihlásit.');
      $this->redirect('Sign:in');
    };

    return $control;
  }

}
