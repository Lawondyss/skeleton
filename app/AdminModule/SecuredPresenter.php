<?php
/**
 * Class SecuredPresenter
 * @package App\AdminModule\Presenters
 * @author Ladislav Vondráček
 */

namespace App\AdminModule\Presenters;

abstract class SecuredPresenter extends BasePresenter
{
  protected function startup()
  {
    parent::startup();

    if (!$this->user->isLoggedIn()) {
      $this->redirect('Sign:in');
    }
  }


  protected function beforeRender()
  {
    parent::beforeRender();
  }

}
