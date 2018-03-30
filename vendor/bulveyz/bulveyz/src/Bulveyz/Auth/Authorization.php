<?php

namespace Bulveyz\Auth;

// This class is needed to initialize it in core Bulvez

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