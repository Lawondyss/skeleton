<?php
/**
 * Class BaseService
 * @package App\Model
 * @author Ladislav Vondráček
 */

namespace App\Model;

use Nette\Database\Context;
use Nette\Database\IRow;

abstract class BaseService extends \Nette\Object implements Service
{
  private $table;

  private $db;


  /**
   * @param \Nette\Database\Context $db
   */
  public function __construct(Context $db)
  {
    $this->db = $db;
  }


  /**
   * @param string|null $tableName
   * @return \Nette\Database\Table\Selection
   * @throws \ErrorException
   */
  public function getTable($tableName = null)
  {
    if (isset($tableName)) {
      return $this->db->table($tableName);
    }

    if (!isset($this->table)) {
      $fullyClassName = get_called_class();
      // class name without namespace
      $className = substr(strrchr($fullyClassName, '\\'), 1);

      preg_match_all('~([A-Z]*[a-z]+)~', $className, $matches);
      $rel = $matches[0];
      // remove "Service"
      array_pop($rel);

      if (count($rel) === 0) {
        $msg = sprintf('Service "%s" has bad name, can not derive the name of the table.', $className);
        throw new \ErrorException($msg);
      }

      array_walk($rel, function(&$value){
        $value = strtolower($value);
      });

      $tableName = implode('_', $rel);
      $this->table = $this->db->table($tableName);
    }

    return $this->table;
  }


  /**
   * @return \Nette\Database\Table\Selection
   */
  public function findAll()
  {
    $result = $this->getTable();
    return $result;
  }


  /**
   * @param array $conditions
   * @return array|IRow[]
   */
  public function findBy(array $conditions)
  {
    $result = $this->findAll()->where($conditions);
    return $result;
  }


  /**
   * @param int $id
   * @return \Nette\Database\Table\IRow
   */
  public function get($id)
  {
    $result = $this->findAll()->get($id);
    return $result;
  }


  /**
   * @param array $conditions
   * @return bool|mixed|IRow
   */
  public function getBy(array $conditions)
  {
    $result = $this->findAll()->where($conditions)->fetch();
    return $result;
  }


  /**
   * @param array|\Traversable|Selection array($column => $value)|\Traversable|Selection for INSERT ... SELECT
   * @return int ID of new record.
   */
  public function insert($data)
  {
    $result = $this->getTable()->insert($data);
    $id = $result->getPrimary();
    return $id;
  }
}
