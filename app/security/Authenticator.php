<?php
/**
 * Class Authenticator
 * @package Security
 * @author Ladislav Vondráček
 */

namespace Security;

use Nette\Security as NS;

class Authenticator extends \Nette\Object implements NS\IAuthenticator
{
  private $userService;
  /**
   * @param \App\Model\UserService $userService
   */
  public function __construct(\App\Model\UserService $userService)
  {
    $this->userService = $userService;
  }


  /**
   * @param array $credentials
   * @return NS\Identity|NS\IIdentity
   * @throws \Nette\Security\AuthenticationException
   */
  public function authenticate(array $credentials)
  {
    list($email, $password) = $credentials;

    $user = $this->userService->findByEmail($email);

    if ($user === false) {
      throw new NS\AuthenticationException('Chybný přihlašovací e-mail.');
    }
    elseif (!self::verifyPassword($password, $user->password)) {
      throw new NS\AuthenticationException('Chybné přihlašovací heslo.');
    }
    elseif (NS\Passwords::needsRehash($user->password)) {
      $user->update(['password' => self::hashPassword($password)]);
    }

    $data = $user->toArray();
    unset($data['password']);

    $identity = new NS\Identity($user->id, $user->role, $data);

    return $identity;
  }


  /**
   * @param string $password
   * @return string
   */
  public static function hashPassword($password)
  {
    $hash = NS\Passwords::hash($password);
    return $hash;
  }


  /**
   * @param string $password
   * @param string $hash
   * @return bool
   */
  public static function verifyPassword($password, $hash)
  {
    $verify = NS\Passwords::verify($password, $hash);
    return $verify;
  }


  /**
   * @return string
   */
  public static function generateToken()
  {
    $token = (string)(microtime(true) * 10000);
    return $token;
  }


  /**
   * @param string|int $token
   * @param int $timeLimit
   * @return bool
   */
  public static function validateToken($token, $timeLimit)
  {
    $expireTime = (int)$token / 10000 + $timeLimit;
    $validity = $expireTime > time();
    return $validity;
  }


  /**
   * @return string
   */
  public static function generateConfirmToken()
  {
    return self::generateToken();
  }
}
