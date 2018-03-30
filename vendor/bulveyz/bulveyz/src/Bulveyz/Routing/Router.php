<?php

namespace Bulveyz\Routing;

/*
 * Router Class
 *
 * Processes routes and connects the requested route from the URL of the site
 */

class Router
{
  private $routerCollection; // All Routers
  private $handler; // Function Router
  private $route; // Route
  private $routeMethod; // Route Method
  public $params = []; // If isset params router add to array
  private $namespace = 'Controllers'; // Path to controllers

  // Load all router from class RouterCollection
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
   *
   * Since the search is performed in certain POST and GET arrays,
   * the script can raise an error about the route, but the route itself
   * can be, but not in POST and in GET, then we additionally check whether it
   * is in the general array for all routes, if there is then the method is not
   * supported if not then the route can not be found
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

      // If request method page GET
      if ($_SERVER['REQUEST_METHOD'] == 'GET') {

        // Parse route only GET
        foreach ($this->routerCollection->getGetRoutes() as $route) {

          // Replace params route and trim (/)
          $pattern = preg_replace("/\{(.*?)\}/", "(?P<$1>[\w-]+)", $route->route);
          $pattern = "#^". trim($pattern, '/') ."$#";
          preg_match($pattern, trim($url, '/'), $matches);

          // If Route finded
          if ($matches) {
            $this->handler = $route->handler;
            $this->route = $route->route;
            $this->routeMethod =  $route->method;
            $this->params[] = $matches;
          }
        }
      }
      // If request method page POST
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Parse route only POST
        foreach ($this->routerCollection->getPostRoutes() as $route) {

          // Replace params route and trim (/)
          $pattern = preg_replace("/\{(.*?)\}/", "(?P<$1>[\w-]+)", $route->route);
          $pattern = "#^". trim($pattern, '/') ."$#";
          preg_match($pattern, trim($url, '/'), $matches);

          // If Route finded
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

          // If handler is function
          if (is_callable($this->handler)) {
            call_user_func($this->handler, (object) $this->params[0]);
          }
          // // If handler is controller and method name
          else {
            $controller_data =  explode('@', $this->handler);
            $controller = $this->namespace . '\\' . ucwords($controller_data[0]) . 'Controller';
            $action = 'action' . ucwords($controller_data[1]);

            // If class exists
            if (class_exists($controller)) {
              $controllerInstance = new $controller;
            } else {
              exit('Class Not Found');
            }

            // If method exists
            if (method_exists($controllerInstance, $action))	{
              call_user_func_array([$controllerInstance, $action], [(object) $this->params[0]]);
            }	else {
              exit('Controller Method Not Found');
            }
          }
        } else {
          exit('HTTP method not allowed');
        }
      }
      // If route not found
      elseif($this->check($this->routerCollection->getAllRoutes(), parse_url($_SERVER['REQUEST_URI'])['path'])) {
        exit('HTTP method not allowed');
      } else {
        exit('Route Not Found');
      }
    }
  }