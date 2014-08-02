<?php

namespace App;

use Nette,
  Nette\Application\Routers\RouteList,
  Nette\Application\Routers\Route,
  Nette\Application\Routers\SimpleRouter;


/**
 * Router factory.
 */
class RouterFactory
{

  /**
   * @return \Nette\Application\IRouter
   */
  public function createRouter()
  {
    $router = new RouteList;

    $routerAdmin = new RouteList('Admin');
    $routerAdmin[] = new Route('admin/<presenter>/<action>[/<id>]', 'Home:default');
    $router[] = $routerAdmin;

    $routerFrontend = new RouteList('Frontend');
    $routerFrontend[] = new Route('<presenter>/<action>[/<id>]', 'Home:default');
    $router[] = $routerFrontend;

    return $router;
  }

}
