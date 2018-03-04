<?php

namespace Bulveyz\Admin;

use RedBeanPHP\R;

class Admin
{
  public static function admin()
  {
    if (isset($_SESSION['admin'])) {
      if (R::count('admina', 'key = ?', array($_SESSION['admin']['key'])) > 0) {
        return true;
      } else {
        return false;
      }
    }
  }
}