<?php

namespace Controllers;

use Bulveyz\Middleware\Middleware;

class HomeController extends Controller
{

  public function actionIndex()
  {
    $this->render('hello', [
      'authMode' => getenv('AUTH'),
      'auth' => Middleware::check('auth')
    ]);
  }
}