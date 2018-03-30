<?php

namespace Bulveyz\Middleware;

/*
 * MiddleWare
 *
 * It implements the work of creating and dividing groups of users with different levels of errors
 */

class Middleware
{
  // Return Redirect to home page for all unresolved users groups (/)
  public static function access(string $userGroup)
  {
    if (!isset($_SESSION[$userGroup])) {
      redirect('/');
    }
  }

  // Return Fatal Error
  public static function fatalAccess(string $userGroup, string $error)
  {
    if (!isset($_SESSION[$userGroup])) {
      exit($error);
    }
  }

  // Return Redirect to home page for onle one unresolved users group (/)
  public static function elseAccess(string $userGroup)
  {
    if (isset($_SESSION[$userGroup])) {
      redirect('/');
    }
  }

  // Return Fatal Error for one users group
  public static function fatalElseAccess(string $userGroup, string $error)
  {
    if (isset($_SESSION[$userGroup])) {
      exit($error);
    }
  }


  // Check of users group
  public static function check($session)
  {
    if (isset($_SESSION[$session])) {
      return true;
    } else {
      return false;
    }
  }
}