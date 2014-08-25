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
      if (!is_numeric($id)) {
        $msg = sprintf('Chybný formát ID uživatele. ID "%s" je typu "%s".', $id, gettype($id));
        $e = new \InvalidArgumentException($msg);

        $this->errorMessage('Chybná identifikace uživatele.', $e);
        $this->redirect('default');
      }

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
    dump($id);
  }


  public function handleRemoveAccount($id)
  {
    dump($id);
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
}
