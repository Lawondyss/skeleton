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

  /** @var \Kdyby\Translation\Translator */
  protected $translator;

  /** @var string */
  private $type;


  public function __construct(\Kdyby\Translation\Translator $translator)
  {
    $this->translator = $translator;
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
        $file = '/signin.latte';
        break;
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

    $form->setTranslator($this->translator);

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
    $form->addText('email', 'email')
      ->setType('email')
      ->setRequired()
      ->addRule($form::EMAIL)
      ->getControlPrototype()
        ->placeholder($this->translator->translate('email'))
        ->autofocus(true);

    $form->addPassword('password', 'password')
      ->setRequired()
      ->getControlPrototype()
        ->placeholder($this->translator->translate('password'));

    $form->addCheckbox('remember', 'remember');

    $form->addSubmit('send', 'login');
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
      $form->addError($this->translator->translate('incorrectLogin'));
    }
  }
}


interface AccountFormsFactory
{
  /** @return \Lawondyss\AccountForms */
  public function create();
}