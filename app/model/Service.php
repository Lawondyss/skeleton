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

  public function fetch($id);

  public function fetchBy(array $conditions);

  public function fetchAll();

  public function fetchAllBy(array $conditions);

  public function insert(\Traversable $data);

  public function update(\Traversable $data, $id);

  public function delete($id);
}
