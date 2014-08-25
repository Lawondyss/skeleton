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
  const GUEST = 'guest';
  const USER = 'user';
  const ADMIN = 'admin';


  public function __construct()
  {
    // define roles
    $this->addRole(self::GUEST);
    $this->addRole(self::USER, self::GUEST);
    $this->addRole(self::ADMIN, self::USER);

    // define admin resources
    $this->addResource('Admin:Home');
    $this->addResource('Admin:Users');
    $this->addResource('Admin:Settings');

    // define permissions
    $this->allow(self::USER, [
      'Admin:Home',
      'Admin:Settings',
    ]);

    $this->allow(self::ADMIN, self::ALL);
  }

}
