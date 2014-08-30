<?php
/**
 * Class AccountForms
 * @package Lawondyss
 * @author Ladislav Vondráček
 */

namespace Lawondyss;

use Nette\Application\UI;
use Security\Authenticator;
use Security\Authorizator;

class AccountForms extends UI\Control
{
  /**
   * Types of forms
   */
  const TYPE_SIGN_IN = 1;

  const TYPE_SIGN_UP = 2;

  const TYPE_FORGET_PASSWORD = 3;

  const TYPE_RESET_PASSWORD = 4;

  const TYPE_CHANGE_PASSWORD = 5;

  const TYPE_VERIFY_PASSWORD = 6;

  const TYPE_CONFIRM_ACCOUNT = 7;

  /**
   * Set names of db columns
   */
  const COLUMN_EMAIL = 'email';

  const COLUMN_PASSWORD = 'password';

  const COLUMN_ROLE = 'role';

  const COLUMN_TOKEN = 'token';

  const COLUMN_CONFIRM = 'confirm';


  /** @var array */
  public $onSuccess = [];

  /** @var array */
  public $onException = [];

  /** @var \Nette\Localization\ITranslator */
  private $translator;

  /** @var \App\Model\Service */
  private $userService;

  /** @var \Nette\Security\User */
  private $user;

  /** @var string */
  private $type;



  /**
   * @param \Nette\Localization\ITranslator $translator
   * @return self
   */
  public function setTranslator(\Nette\Localization\ITranslator $translator)
  {
    $this->translator = $translator;
    return $this;
  }


  /**
   * @param \App\Model\Service $userService
   * @return self
   */
  public function setUserService(\App\Model\Service $userService)
  {
    $this->userService = $userService;
    return $this;
  }


  /**
   * @param \Nette\Security\User $user
   * @return self
   */
  public function setUser(\Nette\Security\User $user)
  {
    $this->user = $user;
    return $this;
  }


  /**
   * @param string $type
   * @return self
   */
  public function setType($type)
  {
    $this->type = $type;
    return $this;
  }


  public function render()
  {
    switch ($this->type) {
      case self::TYPE_SIGN_IN:
      case self::TYPE_SIGN_UP:
      case self::TYPE_FORGET_PASSWORD:
      case self::TYPE_RESET_PASSWORD:
      case self::TYPE_CHANGE_PASSWORD:
      case self::TYPE_VERIFY_PASSWORD:
      case self::TYPE_CONFIRM_ACCOUNT:
      default:
        $file = '/template.latte';
    }

    $this->template->setFile(__DIR__ . '/templates' . $file);
    $this->template->render();
  }


  /**
   * @return \Nette\Application\UI\Form
   * @throws \ErrorException
   */
  protected function createComponentForm()
  {
    $form = new UI\Form;

    switch ($this->type) {
      case self::TYPE_SIGN_IN:
        $this->setupSignInFields($form);
        $callback = $this->processingSignIn;
        break;
      case self::TYPE_SIGN_UP:
        $this->setupSignUpFields($form);
        $callback = $this->processingSignUp;
        break;
      case self::TYPE_FORGET_PASSWORD:
        $this->setupForgetPasswordFields($form);
        $callback = $this->processingForgetPassword;
        break;
      case self::TYPE_RESET_PASSWORD:
        $this->setupResetPasswordFields($form);
        $callback = $this->processingResetPassword;
        break;
      case self::TYPE_CHANGE_PASSWORD:
        $this->setupChangePasswordFields($form);
        $callback = $this->processingChangePassword;
        break;
      case self::TYPE_VERIFY_PASSWORD:
        $this->setupVerifyPasswordFields($form);
        $callback = $this->processingVerifyPassword;
        break;
      case self::TYPE_CONFIRM_ACCOUNT:
        $this->setupConfirmAccountFields($form);
        $callback = $this->processingConfirmAccount;
        break;
      default:
        $msg = isset($this->renderType) ? 'Render type is wrong.' : 'Render type not set';
        throw new \ErrorException($msg);
    }

    $form->onSuccess[] = $callback;

    if (isset($this->translator)) {
      $form->setTranslator($this->translator);
    }

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
        ->autofocus = true;

    $form->addPassword('password', 'Heslo')
      ->setRequired();

    $form->addCheckbox('remember', 'Pamatovat si mě');

    $form->addSubmit('send', 'Přihlásit');
  }


  /**
   * @param UI\Form $form
   * @param \Nette\Utils\ArrayHash $values
   */
  public function processingSignIn(UI\Form $form, $values)
  {
    if ($values->remember) {
      $this->user->setExpiration('14 days', false);
    }
    else {
      $this->user->setExpiration('20 minutes', true);
    }

    try {
      $this->user->login($values->email, $values->password);
      $this->onSuccess($form, $values);
    }
    catch (\Nette\Security\AuthenticationException $e) {
      $form->addError('Chybné přihlášení. Zkontrolujte přihlašovací údaje.');
    }
  }


  /**
   * @param UI\Form $form
   */
  private function setupSignUpFields(UI\Form $form)
  {
    $form->addText('email', 'E-mail')
      ->setType('email')
      ->setRequired()
      ->addRule($form::EMAIL)
      ->getControlPrototype()
        ->autofocus = true;

    $form->addPassword('password', 'Heslo')
      ->setRequired()
      ->addRule($form::FILLED);

    $form->addPassword('passwordConfirm', 'Heslo znova')
      ->setOmitted()
      ->setRequired()
      ->addRule($form::EQUAL, 'Hesla se musí shodovat.', $form['password']);

    $form->addSubmit('send', 'Registrovat');
  }


  /**
   * @param UI\Form $form
   * @param $values
   * @throws \PDOException
   */
  public function processingSignUp(UI\Form $form, $values)
  {
    try {
      $data = [
        self::COLUMN_EMAIL => $values->email,
        self::COLUMN_PASSWORD => Authenticator::hashPassword($values->password),
        self::COLUMN_ROLE => Authorizator::USER,
      ];
      $this->userService->insert($data);

      $this->onSuccess($form, $values);
    }
    catch (\PDOException $e) {
      if ($e->errorInfo[1] == 1062) {
        $form->addError('Tento e-mail je již zaregistrován.');
      }
      else {
        $this->onException($e, $form);
      }
    }
  }


  /**
   * @param UI\Form $form
   */
  private function setupForgetPasswordFields(UI\Form $form)
  {
    $form->addText('email', 'Váš přihlašovací e-mail')
      ->setType('email')
      ->setRequired()
      ->setOption('description', 'Na e-mail bude odeslán odkaz pro reset hesla.')
      ->getControlPrototype()
        ->autofocus = true;

    $form->addSubmit('send', 'Odeslat');
  }


  /**
   * @param UI\Form $form
   * @param $values
   * @throws \ErrorException
   */
  public function processingForgetPassword(UI\Form $form, $values)
  {
    try {
      $user = $this->userService->findByEmail($values->email);
      if ($user === false) {
        $form->addError('Podle e-mailu jste nebyli nalezeni. Zkontrolujte jeho správnost.');
        return;
      }

      $data = [
        self::COLUMN_TOKEN => Authenticator::generateToken(),
      ];
      $user->update($data);
      $values->token = $user->token;

      $this->onSuccess($form, $values);
    }
    catch (\PDOException $e) {
      $this->onException($e, $form);
    }
  }


  /**
   * @param UI\Form $form
   */
  private function setupResetPasswordFields(UI\Form $form)
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

    $form->addSubmit('send', 'Uložit');
  }


  /**
   * @param UI\Form $form
   * @param $values
   * @throws \ErrorException
   */
  public function processingResetPassword(UI\Form $form, $values)
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
        self::COLUMN_PASSWORD => Authenticator::hashPassword($values->password),
        self::COLUMN_TOKEN => null,
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
   * @param UI\Form $form
   */
  private function setupChangePasswordFields(UI\Form $form)
  {
    $form->addPassword('oldPassword', 'Původní heslo')
      ->setRequired();

    $form->addPassword('newPassword', 'Nové heslo')
      ->setRequired();

    $form->addPassword('confirmNewPassword', 'Nové heslo znova')
      ->setOmitted()
      ->setRequired()
      ->addRule($form::EQUAL, 'Nová hesla se musí shodovat.', $form['newPassword']);

    $form->addSubmit('send', 'Uložit');
  }


  /**
   * @param UI\Form $form
   * @param $values
   */
  public function processingChangePassword(UI\Form $form, $values)
  {
    try {
      $user = $this->userService->get($this->user->id);

      $verify = Authenticator::verifyPassword($values->oldPassword, $user->{self::COLUMN_PASSWORD});
      if (!$verify) {
        $form->addError('Chybné heslo.');
        return;
      }

      $data = [
        self::COLUMN_PASSWORD => Authenticator::hashPassword($values->newPassword),
      ];
      $user->update($data);

      $this->onSuccess($form, $values);
    }
    catch (\PDOException $e) {
      $this->onException($e, $form);
    }
  }


  /**
   * @param UI\Form $form
   */
  private function setupVerifyPasswordFields(UI\Form $form)
  {
    $form->addPassword('password', 'Heslo')
      ->setRequired();

    $form->addSubmit('send', 'Odeslat');
  }


  /**
   * @param UI\Form $form
   * @param $values
   */
  public function processingVerifyPassword(UI\Form $form, $values)
  {
    try {
      $user = $this->userService->get($this->user->id);

      $verify = Authenticator::verifyPassword($values->password, $user->{self::COLUMN_PASSWORD});
      if (!$verify) {
        $form->addError('Chybné heslo.');
        return;
      }

      $form->onSuccess($form, $values);
    }
    catch (\PDOException $e) {
      $this->onException($e, $form);
    }
  }


  /**
   * @param UI\Form $form
   */
  private function setupConfirmAccountFields(UI\Form $form)
  {
    $form->addText('code', 'Ověřovací kód')
      ->setRequired()
      ->getControlPrototype()
        ->autofocus = true;

    $form->addSubmit('send', 'Odeslat');
  }


  /**
   * @param UI\Form $form
   * @param $values
   */
  public function processingConfirmAccount(UI\Form $form, $values)
  {
    try {
      $user = $this->userService->get($this->user->id);

      if ($values->code !== $user->{self::COLUMN_CONFIRM}) {
        $form->addError('Ověřovací kód je chybný.');
        return;
      }

      $data = [
        self::COLUMN_CONFIRM => null,
      ];
      $user->update($data);

      $this->onSuccess($form, $values);
    }
    catch (\PDOException $e) {
      $this->onException($e, $form);
    }
  }
}


interface AccountFormsFactory
{
  /** @return \Lawondyss\AccountForms */
  public function create();
}