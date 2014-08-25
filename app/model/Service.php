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

  public function find($id);

  public function findBy(array $conditions);

  public function findAll();

  public function findAllBy(array $conditions);

  public function insert($data);

  public function update($data, $id);

  public function delete($id);
}
