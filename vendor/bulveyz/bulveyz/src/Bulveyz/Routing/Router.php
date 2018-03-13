<?php

namespace Bulveyz\Routing;

class Router
{
  private $routerCollection;
  private $handler;
  private $route;
  private $routeMethod;
  public $params = [];
  private $namespace = 'Controllers';

  public function __construct(RouterCollection $routerCollection)
  {
    $this->routerCollection = $routerCollection;
  }

  /**
   * Check
   * @param $routes
   * @param $url
   * @return bool
   *
   * Check router
   */
  public function check($routes, $url)
  {
    foreach ($routes as $route) {
      $pattern = preg_replace("/\{(.*?)\}/", "(?P<$1>[\w-]+)", $route->route);
      $pattern = "#^". trim($pattern, '/') ."$#";
      preg_match($pattern, trim($url, '/'), $matches);
      if ($matches) {
        return true;
      }
    }
  }

  /**
   * Math
   * @return $this
   *
   * Math all routes and split on request method
   */
  public function math()
  {
    $url = parse_url($_SERVER['REQUEST_URI'])['path'];
      if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        foreach ($this->routerCollection->getGetRoutes() as $route) {
          $pattern = preg_replace("/\{(.*?)\}/", "(?P<$1>[\w-]+)", $route->route);
          $pattern = "#^". trim($pattern, '/') ."$#";
          preg_match($pattern, trim($url, '/'), $matches);
          if ($matches) {
            $this->handler = $route->handler;
            $this->route = $route->route;
            $this->routeMethod =  $route->method;
            $this->params[] = $matches;
          }
        }
      }
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        foreach ($this->routerCollection->getPostRoutes() as $route) {
          $pattern = preg_replace("/\{(.*?)\}/", "(?P<$1>[\w-]+)", $route->route);
          $pattern = "#^". trim($pattern, '/') ."$#";
          preg_match($pattern, trim($url, '/'), $matches);
          if ($matches) {
            $this->handler = $route->handler;
            $this->route = $route->route;
            $this->routeMethod =  $route->method;
            $this->params[] = $matches;
          }
        }
      }

      return $this;
    }

  /**
   * Connect Router
   *
   * If Route fined route parse to controller or method and Classes
   */
    public function connect()
    {
      if ($this->route) {
        if ($_SERVER['REQUEST_METHOD'] === $this->routeMethod[0] || $this->routeMethod[0] == 'ANY') {
          if (is_callable($this->handler)) {
            call_user_func($this->handler, (object) $this->params[0]);
          } else {
            $controller_data =  explode('@', $this->handler);
            $controller = $this->namespace . '\\' . ucwords($controller_data[0]) . 'Controller';
            $action = 'action' . ucwords($controller_data[1]);

            if (class_exists($controller)) {
              $controllerInstance = new $controller;
            } else {
              exit('Class Not Found');
            }

            if (method_exists($controllerInstance, $action))	{
              call_user_func_array([$controllerInstance, $action], [(object) $this->params[0]]);
            }	else {
              exit('Controller Method Not Found');
            }
          }
        } else {
          exit('HTTP method not allowed');
        }
      } elseif($this->check($this->routerCollection->getAllRoutes(), parse_url($_SERVER['REQUEST_URI'])['path'])) {
        exit('HTTP method not allowed');
      } else {
        exit('Route Not Found');
      }
    }
  }