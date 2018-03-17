<?php

namespace Bulveyz\Auth;

class Authorization
{
  use Auth;

  // Check Session or Coockeies
  public function authorization()
  {
    if (isset($_SESSION['auth'])) {
      $this->checkAuthWithSession();
    } elseif (isset($_COOKIE['auth'])) {
      $this->checkAuthWithCookie();
    }
  }
}