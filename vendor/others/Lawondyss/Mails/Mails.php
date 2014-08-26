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
  public function sendResetPassword($from, $to, $link, $webTitle = '')
  {
    $this->mail->setFrom($from);
    $this->mail->addTo($to);

    $body =
      'Zdravíčko,' . PHP_EOL .
      PHP_EOL .
      ($webTitle !== '' ? 'na stránce webu ' . $webTitle . ' ' : '') .
      'byla podána žádost o reset Vašeho hesla.' . PHP_EOL .
      'Ten provedete na odkazu: ' . $link . PHP_EOL . PHP_EOL .
      'Pokud jste o reset hesla nezažádali, tento e-mail ignorujte.' . PHP_EOL .
      PHP_EOL .
      'Přejeme příjemný den.'
    ;
    $this->mail->setBody($body);

    $this->mailer->send($this->mail);
  }
}
