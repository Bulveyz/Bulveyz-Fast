<?php

namespace Bulveyz\Routing;

/*
 * Routes Collection
 *
 * Set new routes and add a arrays
 */

class RouterCollection
{
  private $routesGet = [];
  private $routesPost = [];
  private $routesAll = [];

  /**
   * Any
   * @param $route
   * @param $handler
   *
   * Set POST|GET route
   */
  public function any($route, $handler)
  {
    $this->routesPost[] = new Route($route, $handler, ['ANY']);
    $this->routesGet[] = new Route($route, $handler, ['ANY']);
  }

  /**
   * Get
   * @param $route
   * @param $handler
   *
   * Set GET route
   */
  public function get($route, $handler)
  {
    $this->routesGet[] = new Route($route, $handler, ['GET']);
  }

  /**
   * Post
   * @param $route
   * @param $handler
   *
   * Set POST route
   */
  public function post($route, $handler)
  {
    $this->routesPost[] = new Route($route, $handler, ['POST']);
  }

  /**
   * Get Post Routes
   * @return array
   *
   * Return only post routers
   */
  public function getPostRoutes(): array
  {
    return $this->routesPost;
  }

  /**
   * Get Get Routes
   * @return array
   *
   * Return only get routers
   */
  public function getGetRoutes(): array
  {
    return $this->routesGet;
  }

  /**
   * Get All Routes
   * @return array
   *
   * Return all routers
   */
  public function getAllRoutes(): array
  {
    foreach ($this->routesGet as $rout)
    {
      $this->routesAll[] = $rout;
    }

    foreach ($this->routesPost as $rout)
    {
      $this->routesAll[] = $rout;
    }

    return $this->routesAll;
  }

  /**
   * Start
   *
   * Start routing on web application
   */
  public function start()
  {
    $router = new RouterCollection();
    require_once 'routes/web.php';
    require_once __DIR__ . '/defaultRouters.php';
    $route = new Router($router);
    $route->math()->connect();
  }
}