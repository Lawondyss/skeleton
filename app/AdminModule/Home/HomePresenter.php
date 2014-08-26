<?php

namespace App\AdminModule\Presenters;

class HomePresenter extends BasePresenter
{
  /** @var \App\Model\UserService @autowire */
  protected $userService;


  protected function startup()
  {
    parent::startup();
  }
  
  
  protected function beforeRender()
  {
    parent::beforeRender();
  }


  public function actionDefault()
  {
    if (isset($this->user->identity->token)) {
      $user = $this->userService->get($this->user->id);
      $user->update(['token' => null]);
      $this->user->identity->token = null;
    }
  }
  
}