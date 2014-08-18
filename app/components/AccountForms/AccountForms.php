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


  /** @var array */
  public $onSuccess = [];

  /** @var array */
  public $onException = [];

  /** @var \Nette\Localization\ITranslator */
  private $translator;

  /** @var \App\Model\Service */
  private $userService;

  /** @var string */
  private $type;


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


  public function render()
  {
    switch ($this->type) {
      case self::SIGNIN:
      case self::REGISTER:
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
}


interface AccountFormsFactory
{
  /** @return \Lawondyss\AccountForms */
  public function create();
}