<?php

namespace Bulveyz\Auth;

class Authorization
{
  use Auth;

  public function authorization()
  {
    if (isset($_SESSION['auth'])) {
      $this->checkAuthWithSession();
    } elseif (isset($_COOKIE['auth'])) {
      $this->checkAuthWithCookie();
    }
  }
}