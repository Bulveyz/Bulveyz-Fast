<?php

namespace Controllers;

use Bulveyz\Auth\Auth;

class HomeController extends Controller
{

  public function actionIndex()
  {
    $this->render('hello', [
      'authMode' => getenv('AUTH'),
      'auth' => Auth::auth()
    ]);
  }
}