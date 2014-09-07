<?php
/**
 * Class Service
 * @package App\Model
 * @author Ladislav Vondráček
 */

namespace App\Model;

interface Service
{
  public function getTable($name = null);

  public function get($id);

  public function getBy(array $conditions);

  public function findAll();

  public function findBy(array $conditions);

  public function insert($data);

  public function update($data, $id);

  public function updateBy($data, $conditions);

  public function delete($id);

  public function deleteBy($conditions);

}
