<?php
/**
 * Class Repository
 * @package App\Model\Repositories
 * @author Ladislav Vondráček
 */

namespace App\Model\Repositories;

abstract class Repository extends \LeanMapper\Repository
{
  /**
   * Find row by ID
   *
   * @param int $id
   * @return bool|mixed
   */
  public function find($id)
  {
    $row = $this->connection
      ->select('*')
      ->from($this->getTable())
      ->where('id = %i', $id)
      ->fetch();

    return ($row === false) ? false : $this->createEntity($row);
  }


  /**
   * Find all rows.
   *
   * @return array
   */
  public function findAll()
  {
    $rows = $this->connection
      ->select('*')
      ->from($this->getTable())
      ->fetchAll();

    return $this->createEntities($rows);
  }

}
