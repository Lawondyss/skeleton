<?php
/**
 * Class UserForm
 * @package Lawondyss
 * @author Ladislav Vondráček
 */

namespace Lawondyss;

use Nette\Application\UI;

class UserForm extends UI\Control
{
  /** @var array */
  public $onSuccess = [];

  /** @var array */
  public $onException = [];

  /** @var \Nette\Localization\ITranslator */
  private $translator;

  /** @var \App\Model\UserService */
  private $userService;

  /** @var array */
  private $defaults;

  /** @var array */
  private $roles;


  /**
   * @param \Nette\Localization\ITranslator $translator
   * @return $this
   */
  public function setTranslator(\Nette\Localization\ITranslator $translator)
  {
    $this->translator = $translator;
    return $this;
  }


  /**
   * @param \App\Model\UserService $userService
   * @return $this
   */
  public function setUserService(\App\Model\UserService $userService)
  {
    $this->userService = $userService;
    return $this;
  }


  /**
   * @param array $defaults
   * @return $this
   */
  public function setDefaults(array $defaults)
  {
    $this->defaults = $defaults;
    return $this;
  }


  /**
   * @param array $roles
   * @return $this
   */
  public function setRoles(array $roles)
  {
    $this->roles = $roles;
    return $this;
  }


  public function render()
  {
    $this->template->setFile(__DIR__ . '/template.latte');
    if (isset($this->translator)) {
      $this->template->setTranslator($this->translator);
    }
    $this->template->render();
  }


  /**
   * @return UI\Form
   */
  protected function createComponentForm()
  {
    $form = new UI\Form;

    $form->onSuccess[] = $this->processingForm;

    if (isset($this->translator)) {
      $form->setTranslator($this->translator);
    }

    $this->setupFields($form);

    if (isset($this->defaults)) {
      $form->setValues($this->defaults);
    }

    return $form;
  }


  /**
   * @param UI\Form $form
   */
  private function setupFields(UI\Form $form)
  {
    $form->addText('email', 'E-mail')
      ->setType('email')
      ->setRequired()
      ->addRule($form::EMAIL)
      ->getControlPrototype()
        ->autofocus = true;

    $form->addSelect('role', 'Role', $this->roles)
      ->setPrompt('-- vyberte --')
      ->setRequired();

    $form->addHidden('id');

    $form->addSubmit('send', 'uložit')
      ->getControlPrototype()
        ->addClass('btn-primary');
  }


  /**
   * @param UI\Form $form
   * @param $values
   */
  public function processingForm(UI\Form $form, $values)
  {
    try {
      if (!isset($this->userService)) {
        throw new \ErrorException('Služba "UserService" není definována.');
      }
      $data = [
        'email' => $values->email,
        'role' => $values->role,
      ];
      if ($values->id === '') {
        $this->userService->insert($data);
      }
      else {
        $this->userService->update($data, $values->id);
      }

      $this->onSuccess($form, $values);
    }
    catch (\PDOException $e) {
      $this->onException($e, $form);
    }
    catch (\ErrorException $e) {
      $this->onException($e, $form);
    }
  }
}

interface UserFormFactory
{
  /** @return \Lawondyss\UserForm */
  public function create();
}