<?php

namespace App\Controllers;
use \Core\View;
use App\Models\Post;

class Home extends \Core\Controller
{


  public function indexAction()
  {
    session_start();
    if(isset($_SESSION['username']))
    {
    View::renderTemplate('Home/index.html');
    }
    else
    {
      View::renderTemplate('Home/index.html');
    }
  }
}
