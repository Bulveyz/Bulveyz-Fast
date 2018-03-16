<?php

namespace Bulveyz\Routing;

use Bulveyz\Middleware\Middleware;
use Bulveyz\Middleware\CsrfSecurity;

class Controller
{
  public $params = [];
  public $path;
  public $addGlobal = [];
  public $addGlobalClass = [];

  /**
   * Render
   * @param $view
   * @param array $params
   * @throws \Twig_Error_Loader
   * @throws \Twig_Error_Runtime
   * @throws \Twig_Error_Syntax
   *
   * Create new view and render params to template
   */
  public function render($view, $params = [])
  {
    if (isset($params)) {
      $this->params = $params;
    }

    $loader = new \Twig_Loader_Filesystem('templates/');
    $twig = new \Twig_Environment($loader, array(
        'cache' => false
    ));
    $twig->addGlobal('middleware', new Middleware());
    $twig->addGlobal('csrf_token', CsrfSecurity::generateCsrfToken());
    foreach($this->addGlobal as $addGlobal) {
       $twig->addGlobal($addGlobal['name'], $addGlobal['globalVar']);
    }
    foreach($this->addGlobalClass as $addGlobalClass) {
      $twig->addGlobal($addGlobalClass['name'], new $addGlobalClass['globalClass']());
    }

    echo $twig->render($view . '.tmp', $this->params);

  }

  public function addGlobal($newGlobalName, $newGlobal)
  {
    $this->addGlobal[] = [
      'name' => $newGlobalName,
      'globalVar' => $newGlobal
    ];
  }

  public function addGlobalClass($newGlobalClassName, $newGlobalClass)
  {
    $this->addGlobalClass[] = [
        'name' => $newGlobalClassName,
        'globalClass' => $newGlobalClass
    ];
  }
}