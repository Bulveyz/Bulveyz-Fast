<?php

namespace Bulveyz\Db;

use RedBeanPHP\R;

class Model
{
  /**
   * Get All
   * @return array
   *
   * Return all row from DB
   */
  public static function getAll()
  {
    return R::findAll(self::classRefactor(get_called_class()));
  }

  /**
   * Get One
   * @param $find
   * @param $findTwo
   * @return \RedBeanPHP\OODBBean
   *
   * Return found data from DB
   */
  public static function getOne($find, $findTwo)
  {
    return R::findOne(self::classRefactor(get_called_class()), "{$find} = ?", array($findTwo));
  }

  /**
   * Count
   * @param $find
   * @param $findTwo
   * @return int
   *
   * Return count found rows from BD
   */
  public static function count($find, $findTwo)
  {
    return R::count(self::classRefactor(get_called_class()), "{$find} = ?", array($findTwo));
  }

  /**
   * Load
   * @param $id
   * @return \RedBeanPHP\OODBBean
   *
   * Load row from DB with ID
   */
  public static function load($id)
  {
    return R::load(self::classRefactor(get_called_class()), $id);
  }

  /**
   * Trash
   * @param $id
   *
   * Delete row with id
   */
  public static function trash($id)
  {
    R::trash(self::classRefactor(get_called_class()), $id);
  }

  /**
   * Trash All
   *
   * Trash Table WARNING!!!
   */
  public static function trashAll()
  {
    $loadAll = R::findAll(self::classRefactor(get_called_class()));
    R::trashAll($loadAll);
  }

  /**
   * Create
   * @param array $params
   *
   * Create new table
   */
  public static function create(array $params)
  {
    $create = R::dispense(self::classRefactor(get_called_class()));
    foreach ($params as $param => $value) {
      $create->$param = $value;
    }
    R::store($create);
  }

  /**
   * Change or Add
   * @param $id
   * @param array $params
   *
   * Add or Change data row from DB
   */
  public static function changeOrAdd($id, array $params)
  {
    $change = R::load(self::classRefactor(get_called_class()), $id);
    foreach ($params as $param => $value) {
      $change->$param = $value;
    }
    R::store($change);
  }

  /**
   * Class Refactor
   * @param $className
   * @return string
   *
   * Parse ending class name and parse to table name
   */
  public static function classRefactor($className)
  {
    $className = strtolower(substr($className, strrpos($className, '\\') + 1));
    $ending = substr($className, -2);

    if ($className == 'roof' || $className == 'safe') {
      $className = $className . 's';
    } else {
      if ($ending == 'se') {
        $ending = strrpos($className, 'se');
        $className = substr($className, 0, $ending) . 'ses';
      } elseif ($ending == 'ss') {
        $ending = strrpos($className, 'ss');
        $className = substr($className, 0, $ending) . 'sses';
      } elseif ($ending == 'ch') {
        $className = $className . 'es';
      } elseif ($ending == 'ch') {
        $className = $className . 'es';
      } elseif ($ending == 'fe') {
        $ending = strrpos($className, 'fe');
        $className = substr($className, 0, $ending) . 'ves';
      } else {
        $ending = substr($className, -1);
        if ($ending == 'x') {
          $className = $className . 'es';
        } elseif ($ending == 'y') {
          $ending = strrpos($className, 'y');
          $className = substr($className, 0, $ending) . 'ies';
        } elseif ($ending == 'f') {
          $ending = strrpos($className, 'f');
          $className = substr($className, 0, $ending) . 'ves';
        } else {
          $className = $className . 's';
        }
      }
    }

    return $className;
  }
}