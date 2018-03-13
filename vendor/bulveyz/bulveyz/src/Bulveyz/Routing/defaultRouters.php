<?php

use Bulveyz\Commander\BCommander;

if (getenv('PRODUCTION') == 'false') {
  /*
 * Start Bulveyz Commander
 */
  $router->get('/bcommander', function(){
    $bCommander = new BCommander();
    $bCommander->bCommander();
  });

  /*
   * Create new controller
   */
  $router->post('bcommander/makecontroller', function(){
    $bCommander = new BCommander();
    $bCommander->makeController();
  });

  /*
   * Create new model
   */
  $router->post('bcommander/makemodel', function(){
    $bCommander = new BCommander();
    $bCommander->makeModel();
  });

  /*
   * Create new controller and model for with name
   */
  $router->post('bcommander/makecontrollerandmodel', function(){
    $bCommander = new BCommander();
    $bCommander->makeControllerAndModel();
  });

  /*
   * Create templates auth in root dir (templates/auth)
   */
  $router->get('bcommander/makeauth', function(){
    $bCommander = new BCommander();
    $bCommander->makeAuth();
  });

  /*
   * Drop all rows in table from DB
   */
  $router->post('bcommander/trashall', function(){
    $bCommander = new BCommander();
    $bCommander->trashAll();
  });

  /*
   * Create new controller
   */
  $router->post('bcommander/newadmin', function(){
    $bCommander = new BCommander();
    $bCommander->newAdmin();
  });
}
