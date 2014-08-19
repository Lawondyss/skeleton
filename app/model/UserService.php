<?php
/**
 * Class UserService
 * @package App\Model\UserService
 * @author Ladislav Vondráček
 */

namespace App\Model;

class UserService extends BaseService
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
}
