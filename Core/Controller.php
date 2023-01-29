<?php

namespace Core;

abstract class Controller
{
  // parameters from the matched route
  // @var array
  protected $route_params = [];

  // class constructor
  // @param array route_params Parameters from the route
  // @return void

  public function __construct($route_params)
  {
    $this->route_params = $route_params;
  }

  public function __call($name, $args)
  {
    $method = $name . 'Action';
    if(method_exists($this, $method))
    {
      if($this->before() !== false)
      {
        call_user_func_array([$this, $method], $args);
        $this->after();
      }
    }
    else
    {
      $this->redirect("/");
    }
  }

  protected function before()
  {
    //come here if there is no spesificed before or after method in controller file
  }
  protected function after()
  {
    //come here if there is no spesificed before or after method in controller file
  }
  public static function redirect($path)
  {
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
    {
      $protocol = 'https';
    }
    else
    {
      $protocol = 'http';
    }
    header("Location: $protocol://" . $_SERVER['HTTP_HOST'] . $path);
    exit;
  }
}
