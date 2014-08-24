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
    elseif (!NS\Passwords::verify($password, $user->password)) {
      throw new NS\AuthenticationException('Chybné přihlašovací heslo.');
    }
    elseif (NS\Passwords::needsRehash($user->password)) {
      $user->update(['password' => NS\Passwords::hash($password)]);
    }

    $data = $user->toArray();
    unset($data['password']);

    $identity = new NS\Identity($user->id, $user->role, $data);

    return $identity;
  }
}
