<?php

namespace Bulveyz\Routing;

class Controller
{
  public $params = [];
  public $path;

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

    $loader = new \Twig_Loader_Filesystem(getenv('DIR_TEMPLATES'));
    $twig = new \Twig_Environment($loader, array(
        'cache' => false
    ));

    echo $twig->render($view . '.tmp', $this->params);

  }
}