<?php

namespace Bulveyz\Middleware;

use Bulveyz\Admin\Admin;

class Middleware
{
  public static function access(string $userGroup)
  {
    if (!isset($_SESSION[$userGroup])) {
      if (Admin::admin() == false){
        redirect('/');
      }
    }
  }
}