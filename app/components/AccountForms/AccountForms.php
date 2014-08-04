<?php
/**
 * Class AccountForms
 * @package Lawondyss
 * @author Ladislav Vondráček
 */

namespace Lawondyss;

use Nette\Application\UI;

class AccountForms extends UI\Control
{
  const SIGNIN = 1;


  /** @var array */
  public $onSuccess = [];

  /** @var string */
  private $type;


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
        $file = '/signin.latte';
        break;
      default:
        $file = '/template.latte';
    }

    $this->template->setFile(__DIR__ . $file);
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
      case self::SIGNIN:
        $this->setupInFields($form);
        $callback = $this->processingSignIn;
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
  private function setupInFields(UI\Form $form)
  {
    $form->addText('email', 'E-mail')
      ->setType('email')
      ->setRequired()
      ->addRule($form::EMAIL)
      ->getControlPrototype()
        ->placeholder('E-mail')
        ->autofocus(true);

    $form->addPassword('password', 'Heslo')
      ->setRequired()
      ->getControlPrototype()
        ->placeholder('Heslo');

    $form->addCheckbox('remember', 'Pamatovat si mě');

    $form->addSubmit('send', 'Přihlásit');
  }


  /**
   * @param \Nette\Application\UI\Form
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
      $form->addError('Chybné přihlašovací údaje.');
    }
  }
}


interface AccountFormsFactory
{
  /** @return \Lawondyss\AccountForms */
  public function create();
}