<?php
/**
 * Class Authorizator
 * @package Security
 * @author Ladislav Vondráček
 */

namespace Security;

use Nette\Security as NS;

class Authorizator extends NS\Permission implements NS\IAuthorizator
{
  const USER = 'user';
  const ADMIN = 'admin';


  public function __construct()
  {
    // define roles
    $this->addRole(self::USER);
    $this->addRole(self::ADMIN, self::USER);

    // define admin resources
    $this->addResource('Admin:Home');
    $this->addResource('Admin:Users');

    // define permissions
    $this->allow(self::USER, [
      'Admin:Home'
    ]);

    $this->allow(self::ADMIN, self::ALL);
  }

}
