<?php

namespace Bulveyz\plugins;

use RedBeanPHP\R;

class Paginator
{
  public static $data = [];
  public static $paginate= null;
  public static $count = null;
  public static $offset = null;
  public static $page = 1;

  public static function paginate($data, int $paginate, int $page, string $oredrByDesc = null)
  {
    if ($page == 1) {
       $offset = 0;
    } elseif($page > 1) {
      self::$offset = $paginate * $page - $paginate;
      $offset = self::$offset;
    }

    if ($oredrByDesc) {
      $order = "ORDER BY $oredrByDesc DESC";
    } else {
      $order = null;
    }
    self::$paginate = $paginate;
    self::$page = $page;
    self::$count = R::count($data);
    self::$data = R::findAll($data, "{$order} LIMIT {$offset}, {$paginate}");

    return new self();
  }

  public static function links()
  {
    $pages = ceil(self::$count / self::$paginate);
    $page = self::$page;

    $prev = $page - 1;
    $next = $page + 1;


    for($i = 1; $i <= $pages; $i++) {
      if  ($page == $i) {
        $pagination['links'][] = "<li class='page-item'><a class='page-link active' href='$i'>{$i}</a></li>";
      } else {
        $pagination['links'][] = "<li class='page-item'><a class='page-link' href=$i>$i</a></li>";
      }
    }

    if ($page != 1) {
      $pagination['prev'] = $prev;
    }

    if ($pages > $page) {
      $pagination['next'] = $next;
    }

    if ($page > $pages || $page <= 0) {
      echo "Paginator page ({$page}) not found";
    }

    return $pagination;
  }
}