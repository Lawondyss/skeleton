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
    $routerAdmin[] = new Route('[<locale=cs cs|en>/]admin/<presenter>/<action>[/<id>]', 'Home:default');
    $router[] = $routerAdmin;

    $routerFront = new RouteList('Front');
    $routerFront[] = new Route('[<locale=cs cs|en>/]<presenter>/<action>[/<id>]', 'Home:default');
    $router[] = $routerFront;

    return $router;
  }

}
