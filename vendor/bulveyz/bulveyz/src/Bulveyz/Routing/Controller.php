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
    // Check empty $params or not
    if (isset($params)) {
      $this->params = $params;
    }

    // Init Twig Template Engine   
    $loader = new \Twig_Loader_Filesystem('templates/');
    $twig = new \Twig_Environment($loader, array(
        'cache' => false
    ));

    // Default Global var and classes
    $twig->addGlobal('middleware', new Middleware());
    $twig->addGlobal('csrf_token', CsrfSecurity::generateCsrfToken());
    $twig->addGlobal('homePath', siteURL());

    // Add global var for all templates
    foreach($this->addGlobal as $addGlobal) {
       $twig->addGlobal($addGlobal['name'], $addGlobal['globalVar']);
    }

    // Add global Class for all templates
    foreach($this->addGlobalClass as $addGlobalClass) {
      $twig->addGlobal($addGlobalClass['name'], new $addGlobalClass['globalClass']());
    }

    // Render template
    echo $twig->render($view . '.tmp', $this->params);
  }

  // Function for add global Var
  public function addGlobal($newGlobalName, $newGlobal)
  {
    $this->addGlobal[] = [
      'name' => $newGlobalName,
      'globalVar' => $newGlobal
    ];
  }

  // Function for add global Class
  public function addGlobalClass($newGlobalClassName, $newGlobalClass)
  {
    $this->addGlobalClass[] = [
        'name' => $newGlobalClassName,
        'globalClass' => $newGlobalClass
    ];
  }
}