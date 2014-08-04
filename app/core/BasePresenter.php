<?php

namespace App\Presenters;

use KdybyTests\Autowired\DummyPresenter;
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


  public function formatLayoutTemplateFiles()
  {
    $name = $this->getName();
    $presenter = substr($name, strrpos(':' . $name, ':'));
    $dir = dirname($this->getReflection()->getFileName());
    $dir = substr($dir, 0, strlen($dir)-11);

    $files = [
      $dir . '/' . $presenter . '/@layout.latte',
      $dir . '/@layout.latte',
      $this->context->parameters['appDir'] . '/core/@layout.latte',
    ];
    return $files;
  }


  public function formatTemplateFiles()
  {
    $name = $this->getName();
    $presenter = substr($name, strrpos(':' . $name, ':'));
    $dir = dirname($this->getReflection()->getFileName());
    if (is_dir($substr = substr($dir, 0, strlen($dir)-11))) {
      $dir = $substr;
    }

    $files = [
      $dir . '/' . $presenter . '/templates/' . $this->getView() . '.latte',
      $dir . '/templates/' . $this->getView() . '.latte',
    ];

    return $files;
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
