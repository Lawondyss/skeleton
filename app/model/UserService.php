<?php
/**
 * Class UserService
 * @package App\Model\UserService
 * @author Ladislav Vondráček
 */

namespace App\Model;

use Security\Authorizator;

class UserService extends BaseService
{
  /**
   * @return array
   */
  public function getRoles()
  {
    $roles = [
      Authorizator::USER => 'uživatel',
      Authorizator::ADMIN => 'administrátor',
    ];

    return $roles;
  }


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
