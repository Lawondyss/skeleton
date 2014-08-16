<?php
/**
 * Class UsersPresenter
 * @package App\AdminModule\Presenters
 * @author Ladislav Vondráček
 */

namespace App\AdminModule\Presenters;

class UsersPresenter extends BasePresenter
{
  /** @var \App\Model\Repositories\UserRepository @autowire */
  protected $userRepository;


  /***/
  public function renderDefault()
  {
    $this->template->users = $this->userRepository->findAll();
  }
}
