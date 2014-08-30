<?php
/**
 * Class Mails
 * @package Lawondyss
 * @author Ladislav Vondráček
 */

namespace Lawondyss;

class Mails extends \Nette\Object
{
  /** @var \Nette\Mail\IMailer */
  private $mailer;

  /** @var \Nette\Mail\Message */
  private $mail;


  /**
   * @param \Nette\Mail\IMailer $mailer
   * @param \Nette\Mail\Message $mail
   */
  public function __construct(\Nette\Mail\IMailer $mailer, \Nette\Mail\Message $mail)
  {
    $this->mailer = $mailer;
    $this->mail = $mail;
  }


  /**
   * @param string $from
   * @param string $to
   * @param string $link
   * @param string $webTitle
   */
  public function sendNewPassword($from, $to, $link, $webTitle)
  {
    $subject = 'Žádost o nové heslo pro ' . $webTitle;

    $body =
      'Zdravíčko,' . PHP_EOL .
      PHP_EOL .
      'na stránce webu ' . $webTitle . ' bylo zažádáno o nové heslo.' . PHP_EOL .
      'To zadejte na odkazu: ' . $link . PHP_EOL .
      PHP_EOL .
      'Pokud jste o nové heslo nežádali, tento e-mail ignorujte.' . PHP_EOL .
      PHP_EOL .
      'Přejeme příjemný den.'
    ;

    $this->send($from, $to, $subject, $body);
  }


  /**
   * @param string $from
   * @param string $to
   * @param string $link
   * @param string $code
   * @param string $webTitle
   */
  public function sendConfirmAccount($from, $to, $link, $code, $webTitle)
  {
    $subject = 'Potvrzení účtu pro ' . $webTitle;

    $body =
      'Zdravíčko,' . PHP_EOL .
      PHP_EOL .
      'byli jste zaregistrováni na stránce webu ' . $webTitle . '.' . PHP_EOL .
      PHP_EOL .
      'Po přihlášení zadejte tento kód: ' . $code . PHP_EOL .
      'nebo použijte tento odkaz: ' . $link . PHP_EOL .
      PHP_EOL .
      'Přejeme příjemný den.'
    ;

    $this->send($from, $to, $subject, $body);
  }


  /**
   * @param string $from
   * @param string $to
   * @param string $link
   * @param string $webTitle
   */
  public function sendNewAccount($from, $to, $link, $webTitle)
  {
    $subject = 'Nový účet pro ' . $webTitle;

    $body =
      'Zdravíčko,' . PHP_EOL .
      PHP_EOL .
      'získali jste účet na stránce webu ' . $webTitle . '.' . PHP_EOL .
      PHP_EOL .
      'Na tomto odkazu si zadejte heslo: ' . $link . PHP_EOL .
      PHP_EOL .
      'Přejeme příjemný den.'
    ;

    $this->send($from, $to, $subject, $body);
  }


  /**
   * @param string $from
   * @param string $to
   * @param string $subject
   * @param string $body
   */
  private function send($from, $to, $subject, $body)
  {
    $this->mail->setFrom($from);
    $this->mail->addTo($to);
    $this->mail->setSubject($subject);
    $this->mail->setBody($body);

    $this->mailer->send($this->mail);
  }
}
