<?php

namespace Bulveyz\Middleware;

class Middleware
{
  public static function access(string $userGroup)
  {
    if (!isset($_SESSION[$userGroup])) {
      redirect('/');
    }
  }

  public static function fatalAccess(string $userGroup, string $error)
  {
    if (!isset($_SESSION[$userGroup])) {
      exit($error);
    }
  }

  public static function elseAccess(string $userGroup)
  {
    if (isset($_SESSION[$userGroup])) {
      redirect('/');
    }
  }

  public static function fatalElseAccess(string $userGroup, string $error)
  {
    if (isset($_SESSION[$userGroup])) {
      exit($error);
    }
  }


  public static function check($session)
  {
    if (isset($_SESSION[$session])) {
      return true;
    } else {
      return false;
    }
  }
}