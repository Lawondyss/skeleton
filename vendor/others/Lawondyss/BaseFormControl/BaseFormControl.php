<?php
/**
 * Class BaseFormControl
 * @package Lawondyss
 * @author Ladislav Vondráček
 */

namespace Lawondyss;

use Nette\Application\UI;

abstract class BaseFormControl extends UI\Control
{
  /** @var array */
  public $onSuccess = [];

  /** @var array */
  public $onCancel = [];

  /** @var array */
  public $onException = [];

  /** @var \Nette\Localization\ITranslator */
  protected $translator;

  /** @var array */
  protected $defaults;


  /**
   * @param \Nette\Localization\ITranslator $translator
   * @return self
   */
  public function setTranslator(\Nette\Localization\ITranslator $translator)
  {
    $this->translator = $translator;
    return $this;
  }


  /**
   * @param array $defaults
   * @return self
   */
  public function setDefaults(array $defaults)
  {
    $this->defaults = $defaults;
    return $this;
  }


  public function render($file = null)
  {
    if (!isset($file)) {
      $file = __DIR__ . '/template.latte';
    }

    if (isset($this->translator)) {
      $this->template->translator = $this->translator;
    }

    $this->template->render($file);
  }


  /**
   * @return UI\Form
   */
  protected function createComponentForm()
  {
    $form = new UI\Form;

    $this->setupFields($form);

    if (isset($this->defaults)) {
      $form->setDefaults($this->defaults);
    }

    $form->onSuccess[] = $this->processingForm;

    return $form;
  }


  /**
   * @param UI\Form $form
   * @param $values
   */
  public function processingForm(UI\Form $form, $values)
  {
    dump($values);
  }


  /**
   * @param UI\Form $form
   */
  protected function setupFields(UI\Form $form)
  {
    $form->addProtection('Čas pro odeslání formuláře vypršel. Obnovte stránku.');
  }

}
