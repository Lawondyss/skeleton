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
}
