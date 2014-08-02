<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use WebLoader\Nette\CssLoader;

abstract class BasePresenter extends Presenter
{
  /** @var \WebLoader\Nette\LoaderFactory @inject */
  public $webloader;


  protected function startup()
  {
    parent::startup();
  }


  protected function beforeRender()
  {
    parent::beforeRender();

    $this->template->title = $this->getAppParameter('title');
    $this->template->description = $this->getAppParameter('description');
    $this->template->keywords = implode(',', $this->getAppParameter('keywords'));
  }


  /**
   * @param string $name
   * @return mixed
   */
  protected function getAppParameter($name)
  {
    $appParameters = $this->context->parameters['app'];

    if (!array_key_exists($name, $appParameters)) {
      $msg = sprintf('Parameter "%s" not exists.', $name);
      throw new \InvalidArgumentException($msg);
    }

    return $appParameters[$name];
  }


  /**
   * @return \WebLoader\Nette\CssLoader
   */
  protected function createComponentCssScreen()
  {
    $control = $this->webloader->createCssLoader('screen');
    $control->setMedia('screen,projection,tv');

    return $control;
  }


  /**
   * @return \WebLoader\Nette\CssLoader
   */
  protected function createComponentCssPrint()
  {
    $control = $this->webloader->createCssLoader('print');
    $control->setMedia('print');

    return $control;
  }


  /**
   * @return \WebLoader\Nette\JavaScriptLoader
   */
  protected function createComponentJs()
  {
    $control = $this->webloader->createJavaScriptLoader('default');

    return $control;
  }
}
