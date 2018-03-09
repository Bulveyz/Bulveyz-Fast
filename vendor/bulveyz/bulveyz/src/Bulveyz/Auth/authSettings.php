<?php

use Bulveyz\Middleware\Middleware;

/** @var \Bulveyz\Routing\RouterCollection $router */
$router->any('login', function (){
    Middleware::access('guest');
    if (getenv('AUTH_TEMPLATES_DEFAULT') == 'true') {
      $loader = new \Twig_Loader_Filesystem(__DIR__ . '/templates/');
    } else {
      $loader = new \Twig_Loader_Filesystem('templates/auth/');
    }
    $twig = new \Twig_Environment($loader, array(
        'cache' => false
    ));
    echo $twig->render('login.tmp', [
        'email' => @$_POST['email']
    ]);
});

$router->any('register', function (){
  Middleware::access('guest');
  if (getenv('AUTH_TEMPLATES_DEFAULT') == 'true') {
    $loader = new \Twig_Loader_Filesystem(__DIR__ . '/templates/');
  } else {
    $loader = new \Twig_Loader_Filesystem('templates/auth/');
  }
  $twig = new \Twig_Environment($loader, array(
      'cache' => false
  ));
  echo $twig->render('register.tmp', [
      'name' => @$_POST['name'],
      'email' => @$_POST['email']
  ]);
});

$router->any('reset', function (){
  Middleware::access('guest');
  $reset = new Bulveyz\Auth\Auth();
  $reset->requestReset();
  if (getenv('AUTH_TEMPLATES_DEFAULT') == 'true') {
    $loader = new \Twig_Loader_Filesystem(__DIR__ . '/templates/');
  } else {
    $loader = new \Twig_Loader_Filesystem('templates/auth/');
  }
  $twig = new \Twig_Environment($loader, array(
      'cache' => false
  ));
  echo $twig->render('reset.tmp');
});

$router->any('restore/{token}', function ($request){
  Middleware::access('guest');
  $reset = new Bulveyz\Auth\Auth();
  $reset->resetPassword($request->token);
  if (getenv('AUTH_TEMPLATES_DEFAULT') == 'true') {
    $loader = new \Twig_Loader_Filesystem(__DIR__ . '/templates/');
  } else {
    $loader = new \Twig_Loader_Filesystem('templates/auth/');
  }
  $twig = new \Twig_Environment($loader, array(
      'cache' => false
  ));
  echo $twig->render('restore.tmp');
});

$router->get('/logout', function (){
  if (isset($_SESSION['auth'])) {
    $logout = new Bulveyz\Auth\Auth();
    $logout->userExit($_SESSION['auth']['token']);
  } else {
    redirect('/');
  }
});