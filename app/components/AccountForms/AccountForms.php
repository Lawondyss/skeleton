<?php
/**
 * Class AccountForms
 * @package Lawondyss
 * @author Ladislav Vondráček
 */

namespace Lawondyss;

use Nette\Application\UI;
use Nette\Security\Passwords;

class AccountForms extends UI\Control
{
  const SIGNIN = 1;

  const REGISTER = 2;

  const FORGET = 3;

  const RESET = 4;


  /** @var array */
  public $onSuccess = [];

  /** @var array */
  public $onException = [];

  /** @var \Nette\Mail\IMailer */
  private $mailer;

  /** @var \Nette\Localization\ITranslator */
  private $translator;

  /** @var \App\Model\Service */
  private $userService;

  /** @var string */
  private $type;

  /** @var string */
  private $emailFrom;


  /**
   * @param \Nette\Mail\IMailer $mailer
   */
  public function setMailer(\Nette\Mail\IMailer $mailer)
  {
    $this->mailer = $mailer;
  }


  /**
   * @param \Nette\Localization\ITranslator $translator
   */
  public function setTranslator(\Nette\Localization\ITranslator $translator)
  {
    $this->translator = $translator;
  }


  /**
   * @param \App\Model\Service $userService
   */
  public function setUserService(\App\Model\Service $userService)
  {
    $this->userService = $userService;
  }


  /**
   * @param string
   */
  public function setType($type)
  {
    $this->type = $type;
  }


  /**
   * @param string $emailFrom
   */
  public function setEmailFrom($emailFrom)
  {
    $this->emailFrom = $emailFrom;
  }


  public function render()
  {
    switch ($this->type) {
      case self::SIGNIN:
      case self::REGISTER:
      case self::FORGET:
      case self::RESET:
      default:
        $file = '/template.latte';
    }

    $this->template->setFile(__DIR__ . '/templates' . $file);

    if (isset($this->translator)) {
      $this->template->setTranslator($this->translator);
    }
    $this->template->render();
  }


  /**
   * @return \Nette\Application\UI\Form
   * @throws \ErrorException
   */
  protected function createComponentForm()
  {
    $form = new UI\Form;

    if (isset($this->translator)) {
      $form->setTranslator($this->translator);
    }

    switch ($this->type) {
      case self::SIGNIN:
        $this->setupSignInFields($form);
        $callback = $this->processingSignIn;
        break;
      case self::REGISTER:
        $this->setupRegisterFields($form);
        $callback = $this->processingRegister;
        break;
      case self::FORGET:
        $this->setupForgetFields($form);
        $callback = $this->processingForget;
        break;
      case self::RESET:
        $this->setupResetFields($form);
        $callback = $this->processingReset;
        break;
      default:
        $msg = isset($this->renderType) ? 'Render type is wrong.' : 'Render type not set';
        throw new \ErrorException($msg);
    }

    $form->onSuccess[] = $callback;

    return $form;
  }


  /**
   * @param \Nette\Application\UI\Form
   */
  private function setupSignInFields(UI\Form $form)
  {
    $form->addText('email', 'E-mail')
      ->setType('email')
      ->setRequired()
      ->addRule($form::EMAIL)
      ->getControlPrototype()
        ->autofocus(true);

    $form->addPassword('password', 'Heslo')
      ->setRequired();

    $form->addCheckbox('remember', 'Pamatovat si mě');

    $form->addSubmit('send', 'Přihlásit')
      ->getControlPrototype()
        ->addClass('btn-primary');
  }


  /**
   * @param UI\Form $form
   * @param \Nette\Utils\ArrayHash $values
   */
  public function processingSignIn(UI\Form $form, $values)
  {
    if ($values->remember) {
      $this->presenter->user->setExpiration('14 days', false);
    }
    else {
      $this->presenter->user->setExpiration('20 minutes', true);
    }

    try {
      $this->presenter->user->login($values->email, $values->password);
      $this->onSuccess($form, $values);
    }
    catch (\Nette\Security\AuthenticationException $e) {
      $form->addError('Chybné přihlášení. Zkontrolujte přihlašovací údaje.');
    }
  }


  /**
   * @param UI\Form $form
   */
  private function setupRegisterFields(UI\Form $form)
  {
    $form->addText('email', 'E-mail')
      ->setType('email')
      ->setRequired()
      ->addRule($form::EMAIL)
      ->getControlPrototype()
        ->autofocus(true);

    $form->addPassword('password', 'Heslo')
      ->setRequired()
      ->addRule($form::FILLED);

    $form->addPassword('passwordConfirm', 'Heslo znova')
      ->setOmitted()
      ->setRequired()
      ->addRule($form::EQUAL, 'Hesla se musí shodovat.', $form['password']);

    $form->addSubmit('send', 'Registrovat')
      ->getControlPrototype()
        ->addClass('btn-primary');
  }


  /**
   * @param UI\Form $form
   * @param $values
   * @throws \PDOException
   */
  public function processingRegister(UI\Form $form, $values)
  {
    try {
      $values->role = \App\Model\UserService::ROLE_USER;
      $values->password = Passwords::hash($values->password);
      $this->userService->insert($values);

      $this->onSuccess($form, $values);
    }
    catch (\PDOException $e) {
      if ($e->errorInfo[1] == 1062) {
        $form['email']->addError('Tento e-mail je již zaregistrován.');
      }
      else {
        $this->onException($e, $form);
      }
    }
  }


  /**
   * @param UI\Form $form
   */
  private function setupForgetFields(UI\Form $form)
  {
    $form->addText('email', 'Váš přihlašovací e-mail')
      ->setRequired()
      ->setOption('description', 'Na e-mail bude odeslán odkaz pro reset hesla.')
      ->getControlPrototype()
        ->autofocus = true;

    $form->addSubmit('send', 'Zaslat')
      ->getControlPrototype()
        ->addClass('btn-primary');
  }


  /**
   * @param UI\Form $form
   * @param $values
   * @throws \ErrorException
   */
  public function processingForget(UI\Form $form, $values)
  {
    try {
      $user = $this->userService->findByEmail($values->email);
      if ($user === false) {
        $form->addError('Podle e-mailu jste nebyli nalezeni. Zkontrolujte jeho správnost.');
        return;
      }

      $user->update(['token' => (string)(microtime(true) * 10000)]);

      $this->sendResetMail($this->emailFrom, $user->email, $user->token);
    }
    catch (\PDOException $e) {
      $this->onException($e, $form);
    }

    $this->onSuccess($form, $values);
  }


  /**
   * @param UI\Form $form
   */
  public function setupResetFields(UI\Form $form)
  {
    $form->addPassword('password', 'Heslo')
      ->setRequired()
      ->getControlPrototype()
        ->autofocus = true;

    $form->addPassword('passwordConfirm', 'Heslo znova')
      ->setRequired()
      ->setOmitted()
      ->addRule($form::EQUAL, 'Hesla se musí shodovat.', $form['password']);

    $token = $this->presenter->getParameter('token');
    $form->addHidden('token', $token);

    $form->addSubmit('send', 'Uložit')
      ->getControlPrototype()
        ->addClass('btn-primary');
  }


  /**
   * @param UI\Form $form
   * @param $values
   * @throws \ErrorException
   */
  public function processingReset(UI\Form $form, $values)
  {
    try {
      if (!isset($values->token)) {
        throw new \ErrorException('Token nebyl nalezen v odeslaných datech.');
      }

      $user = $this->userService->findByToken($values->token);
      if ($user === false) {
        $msg = sprintf('Podle tokenu "%s" nebyl dohledán uživatel.', $values->token);
        throw new \ErrorException($msg);
      }

      $data = [
        'password' => Passwords::hash($values->password),
        'token' => null,
      ];
      $user->update($data);

      $this->onSuccess($form, $values);
    }
    catch (\PDOException $e) {
      $this->onException($e, $form);
    }
    catch (\ErrorException $e) {
      $this->onException($e, $form);
    }
  }


  /**
   * @param string $from
   * @param string $to
   * @param string|int $token
   * @param string $webTitle
   */
  private function sendResetMail($from, $to, $token, $webTitle = '')
  {
    $mail = new \Nette\Mail\Message;
    $mail->setFrom($from);
    $mail->addTo($to);

    $link = $_SERVER['HTTP_HOST'] . $this->presenter->link('Sign:reset', array('token' => $token));
    $body =
      'Zdravíčko,' . PHP_EOL .
      PHP_EOL .
      ($webTitle !== '' ? 'na stránce webu ' . webTtitle . ' ' : '') .
      'byla podána žádost o reset Vašeho hesla.' . PHP_EOL .
      'Ten provedete na odkazu: ' . $link . PHP_EOL . PHP_EOL .
      'Pokud jste o reset hesla nezažádali, tento e-mail ignorujte.' . PHP_EOL .
      PHP_EOL .
      'Přejeme příjemný den.'
    ;
    $mail->setBody($body);

    $this->mailer->send($mail);
  }

}


interface AccountFormsFactory
{
  /** @return \Lawondyss\AccountForms */
  public function create();
}