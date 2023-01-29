<?php

namespace Core;
use Core\Controller;

/**
* Router
*
* PHP version 5.4
*/

class Router
{
  /**
  * Associative array of routes (the routing table)
  * @var array
  */
  protected $routes = [];

  /**
  * Parameters from the matched route
  * @var array
  */
  protected $params = [];

  /**
  * Add a route to the routing table
  *
  * @param string $route The route URL
  * @param array $params Parameters (controller, action, etc)
  *
  * @return void
  */

  public function add($route, $params = [])
  {
    // Convert the route to a regular expression: escape forward slashes
    $route = preg_replace('/\//', '\\/', $route);

    // Convert variables e.g. {controller}
    $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

    // Convert variables with custom regular expressions e.g. {id:\d+}
    $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

    // Add start and end delimiters, and case insensitive flag
    $route = '/^' . $route . '$/i';

    $this->routes[$route] = $params;
  }

  /**
  * Get all the routes from the routing table
  *
  * @return array
  */

  public function getRoutes()
  {
    return $this->routes;
  }

  /**
  * Match the route to the routes in the routing table, setting the $params
  * property if a route is found.
  *
  * @param string $url The route URL
  *
  * @return boolean true if a match found, false otherwise
  */
  public function match($url)
  {
    foreach ($this->routes as $route => $params)
    {
      if(preg_match($route, $url, $matches))
      {
        foreach ($matches as $key => $match)
        {
          if(is_string($key))
          {
            $params[$key] = $match;
          }
        }
        $this->params = $params;
        return true;
      }
    }
    return false;
  }

  /**
  * Get the currently matched parameters
  *
  * @return array
  */
  public function getParams()
  {
    return $this->params;
  }

  public function dispatch($url)
  {
    $url = $this->removeQueryStringVariables($url);
    if($this->match($url))
    {
      $controller = $this->params['controller'];
      $controller = $this->convertToStudlyCaps($controller);
      //$controller = "App\Controllers\\$controller";
      $controller = $this->getNamespace() . $controller;
      if(class_exists($controller))
      {
        $controller_object = new $controller($this->params);

        $action = $this->params['action'];
        $action = $this->convertToCamelCase($action);

        if (preg_match('/action$/i', $action) == 0)
        {
            $controller_object->$action();
        }
        else
        {
          Controller::redirect("/");
        }
      }
      else
      {
        Controller::redirect("/");
      }
    }
    else
    {
      Controller::redirect("/");
    }
  }

  protected function convertToStudlyCaps($string)
  {
    return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
  }
  protected function convertToCamelCase($string)
  {
    return lcfirst($this->convertToStudlyCaps($string));
  }
  protected function removeQueryStringVariables($url)
  {
    if($url != '')
    {
      $parts = explode('&', $url, 2);
      if(strpos($parts[0], '=') === false)
      {
        $url = $parts[0];
      }
      else
      {
        $url = '';
      }
    }
    return $url;
  }

  protected function getNamespace()
  {
    $namespace = 'App\Controllers\\';
    if(array_key_exists('namespace', $this->params))
    {
      $namespace .= $this->params['namespace'] . '\\';
    }
    return $namespace;
  }

}
