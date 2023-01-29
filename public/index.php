<?php

/**
* Front controller
* PHP version 5.4
*/

/*
*Require the controller class
*require '../App/Controllers/Posts.php';
*require '../Core/Router.php';
*/

require '../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__, 1)); //.env source
$dotenv->load();
$router = new Core\Router();
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);

//display the routing table

/*echo '<pre>';
echo htmlspecialchars(print_r($router->getRoutes(), true));
echo '</pre>';

//match the requested route
$url = $_SERVER['QUERY_STRING'];
if($router->match($url))
{
  echo '<pre>';
  var_dump($router->getParams());
  echo '</pre>';
}
else
{
  echo "No route found for URL '$url'";
}
*/
$router->dispatch($_SERVER['QUERY_STRING']);
