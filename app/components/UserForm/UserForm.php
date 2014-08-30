<?php
/**
 * Class UserForm
 * @package Lawondyss
 * @author Ladislav Vondráček
 */

namespace Lawondyss;

use Nette\Application\UI;

class UserForm extends BaseFormControl
{
  /** @var \App\Model\UserService */
  private $userService;

  /** @var array */
  private $roles;


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
   * @param array $roles
   * @return $this
   */
  public function setRoles(array $roles)
  {
    $this->roles = $roles;
    return $this;
  }


  /**
   * @param UI\Form $form
   */
  protected function setupFields(UI\Form $form)
  {
    parent::setupFields($form);

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

    $form->addSubmit('send', 'Uložit');

    $form->addSubmit('cancel', 'Zrušit')
      ->setValidationScope(false)
      ->getControlPrototype()
        ->addClass('btn-link');
  }


  /**
   * @param UI\Form $form
   * @param $values
   */
  public function processingForm(UI\Form $form, $values)
  {
    if ($form->submitted->name === 'cancel') {
      $this->onCancel($form, $values);
      return;
    }

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
      if ($e->errorInfo[1] == 1062) {
        $form->addError('Tento e-mail je již zaregistrován.');
      }
      else {
        $this->onException($e, $form);
      }
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