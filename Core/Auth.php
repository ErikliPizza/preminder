<?php

namespace Core;
use Core\Controller;
/**
* Router
*
* PHP version 5.4
*/

class Auth
{
  public static function isLoggedIn() //we are not going to create of this class, so we make it static
  {
    if(isset($_SESSION['userid']))
    {
      if($_SESSION['userid'])
      {
        return true;
      }
      else
      {
        return false;
      }
    }
  }


  /**
  *USE IT @param RESTRICTED_AREAS
  */
  public static function requireLogin()
  {
    if(!static::isLoggedIn())
    {
      Controller::redirect("/users/sign");
      die();
    }
  }


  /**
  *SET SESSION if the log in informations matches
  *$un @param USERNAME which come from login.php
  */
  public static function login($username, $id)
  {
    session_regenerate_id(true);
    $_SESSION['username'] = $username;
    $_SESSION['userid'] = $id;
  }
  /**
  *LOG OUT AND CLEAR THE SESSION
  */
  public static function logout()
  {
    $_SESSION = array();
    if(ini_get("session.use_cookies"))
    {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000,
      $params["path"], $params["domain"],
      $params["secure"], $params["httponly"]
      );
    }
    session_destroy();
  }
}
