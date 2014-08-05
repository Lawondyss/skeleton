<?php
/**
 * Class User
 * @package App\Model\Entities
 * @author Ladislav Vondráček
 */

namespace App\Model\Entities;

/**
 * @property int $id
 * @property string email
 * @property string password
 * @property string role
 * @property string|null token
 * @property string|null token_timestamp
 */
class User extends \LeanMapper\Entity
{
}
