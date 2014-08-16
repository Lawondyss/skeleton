<?php
/**
 * Class Translator
 * @package Lawondyss
 * @author Ladislav Vondráček
 */

namespace Lawondyss;

class Translator implements \Nette\Localization\ITranslator
{
  /**
   * @param string $message
   * @param int|null $count
   * @return string
   */
  public function translate($message, $count = null)
  {
    // currently not implemented any translation
    $trans = sprintf($message, $count);
    return $trans;
  }
}
