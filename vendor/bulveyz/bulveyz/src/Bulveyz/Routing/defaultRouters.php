<?php

use Bulveyz\Commander\BCommander;

if (getenv('PRODUCTION') == 'false') {
  $router->get('bcommander', function(){
    $bCommander = new BCommander();
    $bCommander->bCommander();
  });

  $router->post('bcommander/makecontroller', function(){
    $bCommander = new BCommander();
    $bCommander->makeController();
  });

  $router->post('bcommander/makemodel', function(){
    $bCommander = new BCommander();
    $bCommander->makeModel();
  });

  $router->post('bcommander/makecontrollerandmodel', function(){
    $bCommander = new BCommander();
    $bCommander->makeControllerAndModel();
  });

  $router->get('bcommander/makeauth', function(){
    $bCommander = new BCommander();
    $bCommander->makeAuth();
  });

  $router->post('bcommander/trashall', function(){
    $bCommander = new BCommander();
    $bCommander->trashAll();
  });

  $router->post('bcommander/newadmin', function(){
    $bCommander = new BCommander();
    $bCommander->newAdmin();
  });
}