<?php

namespace Controllers;

class HomeController extends Controller
{
  public function actionIndex()
  {
    $this->render('welcome');
  }
}