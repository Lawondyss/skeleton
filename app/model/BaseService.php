<?php
/**
 * Class BaseService
 * @package App\Model
 * @author Ladislav Vondráček
 */

namespace App\Model;

use Nette\Database\Context;

abstract class BaseService extends \Nette\Object
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
      $table = $this->db->table($tableName);
    }
    elseif (isset($this->table)) {
      $table = $this->table;
    }
    else {
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
      $table = $this->db->table($tableName);
    }

    return $table;
  }


  /**
   * @param int $id
   * @return bool|mixed|IRow
   */
  public function find($id)
  {
    $result = $this->getTable()->wherePrimary($id)->fetch();
    return $result;
  }


  /**
   * @param array $conditions
   * @return bool|mixed|IRow
   */
  public function findBy($conditions)
  {
    $resutl = $this->getTable()->where($conditions)->fetch();
    return $resutl;
  }


  /**
   * @return array|IRow[]
   */
  public function findAll()
  {
    $result = $this->getTable()->fetchAll();
    return $result;
  }


  /**
   * @param array $conditions
   * @return array|IRow[]
   */
  public function findAllBy($conditions)
  {
    $result = $this->getTable()->where($conditions)->fetchAll();
    return $result;
  }


  /**
   * @param \Traversable $data
   * @return int ID of new record.
   */
  public function insert($data)
  {
    $result = $this->getTable()->insert($data);
    $id = $result->getPrimary();
    return $id;
  }


  /**
   * @param \Traversable $data
   * @param int $id
   * @return int Count of affected rows.
   */
  public function update($data, $id)
  {
    $affectedRows = $this->getTable()->wherePrimary($id)->update($data);
    return $affectedRows;
  }


  /**
   * @param int $id
   * @return int Count of affected rows.
   */
  public function delete($id)
  {
    $affectedRows = $this->getTable()->wherePrimary($id)->delete();
    return $affectedRows;
  }
}
