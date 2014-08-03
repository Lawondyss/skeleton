<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use WebLoader\Nette\CssLoader;

abstract class BasePresenter extends Presenter
{
  use \Kdyby\Autowired\AutowireProperties;
  use \Kdyby\Autowired\AutowireComponentFactories;


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
   * @param \WebLoader\Nette\LoaderFactory
   * @return \WebLoader\Nette\CssLoader
   */
  protected function createComponentCssScreen(\WebLoader\Nette\LoaderFactory $webloader)
  {
    $control = $webloader->createCssLoader('screen');
    $control->setMedia('screen,projection,tv');

    return $control;
  }


  /**
   * @param \WebLoader\Nette\LoaderFactory
   * @return \WebLoader\Nette\CssLoader
   */
  protected function createComponentCssPrint(\WebLoader\Nette\LoaderFactory $webloader)
  {
    $control = $webloader->createCssLoader('print');
    $control->setMedia('print');

    return $control;
  }


  /**
   * @param \WebLoader\Nette\LoaderFactory
   * @return \WebLoader\Nette\JavaScriptLoader
   */
  protected function createComponentJs(\WebLoader\Nette\LoaderFactory $webloader)
  {
    $control = $webloader->createJavaScriptLoader('default');

    return $control;
  }
}
