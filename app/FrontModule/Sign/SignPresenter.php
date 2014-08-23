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
   * @param \Nette\Mail\Message $mail
   * @return \Lawondyss\AccountForms
   */
  protected function createComponentForgetForm(\Lawondyss\AccountFormsFactory $accountFormsFactory, \App\Model\UserService $userService, \Nette\Mail\IMailer $mailer, \Nette\Mail\Message $mail)
  {
    $control = $accountFormsFactory->create();
    $control->setTranslator($this->translator)
      ->setType($control::FORGET)
      ->setUserService($userService);

    $control->onException[] = function($e) {
      $this->errorMessage('Něco je špatně. Zkuste to později, snad už to bude lepší.', $e);
    };

    $control->onSuccess[] = function(\Nette\Application\UI\Form $form, $values) use ($mailer, $mail) {
      $from = $this->getAppParameter('email.noreply');
      $to = $values->email;
      $token = $values->token;
      $webTitle = $this->getAppParameter('title');
      $this->sendForgetMail($mail, $mailer, $from, $to, $token, $webTitle);
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


  /**
   * @param \Nette\Mail\Message $mail
   * @param \Nette\Mail\IMailer $mailer
   * @param string $from
   * @param string $to
   * @param string|int $token
   * @param string $webTitle
   */
  private function sendForgetMail(\Nette\Mail\Message $mail, \Nette\Mail\IMailer $mailer, $from, $to, $token, $webTitle = '')
  {
    $mail->setFrom($from);
    $mail->addTo($to);

    $link = $_SERVER['HTTP_HOST'] . $this->link('Sign:reset', array('token' => $token));
    $body =
      'Zdravíčko,' . PHP_EOL .
      PHP_EOL .
      ($webTitle !== '' ? 'na stránce webu ' . $webTitle . ' ' : '') .
      'byla podána žádost o reset Vašeho hesla.' . PHP_EOL .
      'Ten provedete na odkazu: ' . $link . PHP_EOL . PHP_EOL .
      'Pokud jste o reset hesla nezažádali, tento e-mail ignorujte.' . PHP_EOL .
      PHP_EOL .
      'Přejeme příjemný den.'
    ;
    $mail->setBody($body);

    $mailer->send($mail);
  }
}
