<?php
/**
 * Class UserService
 * @package App\Model\UserService
 * @author Ladislav Vondráček
 */

namespace App\Model;

use Nette\Security\AuthenticationException;
use Nette\Security as NS;

class UserService extends BaseService implements NS\IAuthenticator
{
  const ROLE_USER = 'user';

  const ROLE_ADMIN = 'admin';


  /**
   * @param string $email
   * @return bool|mixed|\Nette\Database\IRow
   */
  public function findByEmail($email)
  {
    $result = $this->findBy(['email' => $email]);
    return $result;
  }


  /**
   * @param string $token
   * @return bool|mixed|\Nette\Database\IRow
   */
  public function findByToken($token)
  {
    $result = $this->findBy(['token' => $token]);
    return $result;
  }


  /**
   * @param array $credentials
   * @return NS\IIdentity
   * @throws \Nette\Security\AuthenticationException
   */
  public function authenticate(array $credentials)
  {
    list($email, $password) = $credentials;

    $row = $this->findByEmail($email);

    if ($row === false) {
      throw new AuthenticationException('Chybný přihlašovací e-mail.');
    }
    elseif (!NS\Passwords::verify($password, $row->password)) {
      throw new AuthenticationException('Chybné přihlašovací heslo.');
    }
    elseif (NS\Passwords::needsRehash($row->password)) {
      $row->update(['password' => NS\Passwords::hash($password)]);
    }

    $data = $row->toArray();
    unset($data['password']);
    $user = new NS\Identity($row->id, $row->role, $data);
    return $user;
  }
}
