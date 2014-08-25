<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use Tracy\Debugger;

abstract class BasePresenter extends Presenter
{
  use \Kdyby\Autowired\AutowireProperties;
  use \Kdyby\Autowired\AutowireComponentFactories;


  /** @persistent */
  public $locale;

  /** @var \Lawondyss\Translator @autowire */
  protected $translator;

  /** @var string */
  protected $defaultErrorMessage = 'Něco je špatně. Zkuste to později, snad už to bude lepší.';


  protected function startup()
  {
    parent::startup();
  }


  protected function beforeRender()
  {
    parent::beforeRender();

    $this->template->setTranslator($this->translator);

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
      $dir . '/core/' . $presenter . '/templates/' . $this->getView() . '.latte',
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
    $values = $this->context->parameters['app'];
    $nextIndex = $name;

    // explode by "." in name to indexes
    while (strpos($nextIndex, '.') !== false) {
      $index = strstr($nextIndex, '.', true);
      $nextIndex = substr(strstr($nextIndex, '.'), 1);

      if (!array_key_exists($index, $values)) {
        $msg = sprintf('Parameter "%s" not exists.', $name);
        throw new \InvalidArgumentException($msg);
      }

      $values = $values[$index];
    }

    $name = $nextIndex;

    if (!array_key_exists($name, $values)) {
      $msg = sprintf('Parameter "%s" not exists.', $name);
      throw new \InvalidArgumentException($msg);
    }

    return $values[$name];
  }


  /**
   * @param string $message
   */
  public function infoMessage($message)
  {
    $this->flashMessage($message, 'info');
  }


  /**
   * @param string $message
   */
  public function successMessage($message)
  {
    $this->flashMessage($message, 'success');
  }


  /**
   * @param string $message
   * @param \Exception|null $e
   */
  public function errorMessage($message, \Exception $e = null)
  {
    $this->flashMessage($message, 'danger');

    if (isset($e)) {
      if(Debugger::$productionMode) {
        Debugger::log($e, Debugger::ERROR);
      }
      else {
        $this->flashMessage(get_class($e) . ': ' . $e->getMessage(), 'info');
      }
    }

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


  /**
   * @param \Lawondyss\MenuFactory $menuFactory
   * @return \Lawondyss\Menu
   */
  protected function createComponentMenu(\Lawondyss\MenuFactory $menuFactory)
  {
    $parameters = $this->context->getParameters();

    $control = $menuFactory->create();
    $control->setTranslator($this->translator)
      ->setCurrent($this->name . ':' . $this->action)
      ->setItems($parameters['menu']['front']);

    return $control;
  }
}
