<?php
/**
 * Class UsersPresenter
 * @package App\AdminModule\Presenters
 * @author Ladislav Vondráček
 */

namespace App\AdminModule\Presenters;

class UsersPresenter extends BasePresenter
{
  /** @var \App\Model\UserService @autowire */
  protected $userService;


  public function renderDefault()
  {
    $this->template->users = $this->userService->findAll();
  }


  public function actionEdit($id)
  {
    if (isset($id)) {
      $this->checkId($id);

      $user = $this->userService->find($id);

      if ($user === false) {
        $msg = sprintf('Uživatel s ID "%s" nebyl nalezen.', $id);
        $e = new \ErrorException($msg);

        $this->errorMessage('Uživatel nebyl nalezen.', $e);
        $this->redirect('default');
      }

      $defaults = [
        'id' => $id,
        'email' => $user->email,
        'role' => $user->role,
      ];
      $this['userForm']->setDefaults($defaults);
    }

    $this->template->isEdit = isset($id);
  }


  public function handleResetPassword($id)
  {
    $this->checkId($id);
    dump($id);
  }


  public function handleRemoveAccount($id)
  {
    try {
      $this->checkId($id);
      $this->userService->delete($id);
      $this->successMessage('Smazáno.');
    }
    catch (\PDOException $e) {
      $this->errorMessage($this->defaultErrorMessage, $e);
    }

    $this->redirect('this');
  }


  /**
   * @param \Lawondyss\UserFormFactory $userFormFactory
   * @return \Lawondyss\UserForm
   */
  protected function createComponentUserForm(\Lawondyss\UserFormFactory $userFormFactory)
  {
    $control = $userFormFactory->create();

    $roles = $this->userService->getRoles();
    $control->setTranslator($this->translator)
      ->setUserService($this->userService)
      ->setRoles($roles);

    $control->onException[] = function($e) {
      $this->errorMessage($this->defaultErrorMessage, $e);
    };

    $control->onSuccess[] = function() {
      $this->successMessage('Uloženo.');
      $this->redirect('default');
    };

    $control->onCancel[] = function() {
      $this->redirect('default');
    };

    return $control;
  }


  /**
   * @param int|string $id
   */
  private function checkId($id)
  {
    if (!is_numeric($id)) {
      $msg = sprintf('Chybný formát ID uživatele. ID "%s" je typu "%s".', $id, gettype($id));
      $e = new \InvalidArgumentException($msg);

      $this->errorMessage('Chybná identifikace uživatele.', $e);
      $this->redirect('default');
    }

    if ($id == $this->user->id) {
      $msg = sprintf('Uživatel ID "%s" chtěl zpracovat záznam vlastního uživatele.', $this->user->id);
      $e = new \InvalidArgumentException($msg);

      $this->errorMessage('Nelze pracovat se záznamem vlastního uživatele.', $e);
      $this->redirect('default');
    }
  }
}
