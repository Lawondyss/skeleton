<?php
/**
 * Class UsersPresenter
 * @package App\AdminModule\Presenters
 * @author Ladislav Vondráček
 */

namespace App\AdminModule\Presenters;

class UsersPresenter extends BasePresenter
{
  /** @var \App\Model\UserService\UserService @autowire */
  protected $userService;


  /***/
  public function renderDefault()
  {
    $this->template->users = $this->userService->findAll();
  }
}
