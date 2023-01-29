<?php

namespace App\Controllers;
use \Core\View;
use \Core\Auth;
use App\Models\User;


class Users extends \Core\Controller
{
  private $username;
  private $password;
  private $mail;
  public $errors = [];
  protected function before()
  {
    session_start();
  }

  protected function after()
  {
  }

  public function logoutAction()
  {
    Auth::logout();
    static::redirect("/");
    die();
  }

  public function loginAction()
  {
    if(Auth::isLoggedIn())
    {
      static::redirect("/");
      die();
    }
    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
      $this->password = $_POST['password'];
      $this->mail = $_POST['mail'];

      $loggedUser = User::authenticate($this->mail, $this->password);
      if($loggedUser)
      {
        Auth::login($loggedUser['username'], $loggedUser['id']);
        static::redirect("/payments/index");
        die();
      }
      else
      {
        View::renderTemplate('User/login.html', [
              'error' => 'incorrect username or password'
            ]);
      }
    }
    else
    {
      View::renderTemplate('User/login.html');
    }
  }
  public function signAction()
  {
    if(Auth::isLoggedIn())
    {
      static::redirect("/payments/index");
      die();
    }
    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
      $this->username = $_POST['username'];
      $this->password = $_POST['password'];
      $this->mail = $_POST['mail'];
      $users = User::getAllUser($this->mail);

      if($users) $this->errors[] = "mail already exist";
      if(strlen($this->username) < 4 || strlen($this->username) > 25 ) $this->errors[] = "invalid username";
      if(strlen($this->password) < 6 || strlen($this->password) > 25 ) $this->errors[] = "invalid password";
      if(strlen($this->mail) < 6 || strlen($this->mail) > 30) $this->errors[] = "invalid mail";
      if($this->errors)
      {
        View::renderTemplate('User/sign.html', [
              'errors' => $this->errors,
              'username' => $this->username,
              'password' => $this->password,
              'mail' => $this->mail
            ]);
        die();
      }
      else
      {
        if(User::registerUser($this->username, $this->password, $this->mail))
        {
          static::redirect("/users/login");
          die();
        }
      }
    }
    else
    {
      View::renderTemplate('User/sign.html');
    }
  }


  public function editAction()
  {
    Auth::requireLogin();
    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
      $this->password = $_POST['password'];
      $this->mail = $_POST['mail'];

      $loggedUser = User::authenticate($this->mail, $this->password);
      if(!$loggedUser)
      {
        View::renderTemplate('User/edit.html', [
              'error' => 'incorrect mail or password'
            ]);
        die();
      }

      $users = User::getAllUser($_POST['newmail']);
      if($users)
      {
        $this->errors[]="new mail is already exist, please try another mail adress, it did not updated";
        $_POST['newmail'] = $this->mail;
      }

      if($_POST['newusername'] == '')
      {
        $_POST['newusername'] = $_SESSION['username'];
      }
      if(strlen($_POST['newusername']) > 25 )
      {
        $this->errors[]="new username is invalid, it did not updated";
        $_POST['newusername'] = $_SESSION['username'];
      }

      if($_POST['newpassword'] == '')
      {
        $_POST['newpassword'] = $this->password;
      }
      if(strlen($_POST['newpassword']) > 25 )
      {
        $this->errors[]="new password is invalid, it did not updated";
        $_POST['newpassword'] = $this->password;
      }

      if($_POST['newmail'] == '')
      {
        $_POST['newmail'] = $this->mail;
      }
      if(strlen($_POST['newmail']) > 30)
      {
        $this->errors[]="new mail is invalid, it did not updated";
        $_POST['newmail'] = $this->mail;
      }

      if(!User::editUser($_POST['newusername'], $_POST['newpassword'], $_POST['newmail'], $_SESSION['userid']))
      {
        View::renderTemplate('User/edit.html', [
              'errors' => "an error occured, your data is did not updated"
            ]);
        die();
      }
      View::renderTemplate('User/edit.html', [
              'errors' => $this->errors,
              'newusername' => $_POST['newusername'],
              'newpassword' => $_POST['newpassword'],
              'newmail' => $_POST['newmail']
            ]);
      $_SESSION['username'] = $_POST['newusername'];
    }
    else
    {
      View::renderTemplate('User/edit.html');
    }
  }

}
