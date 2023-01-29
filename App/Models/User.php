<?php

namespace App\Models;
use PDO;

class User extends \Core\Model
{
  public static function getAllUser($mail)
  {
    try{
      $db = static::getDB();

      $sql = "SELECT mail FROM users
              WHERE mail = :mail";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
      $stmt->execute();
      return $stmt->rowCount();
    }
    catch(PDOExpection $e){
      echo $e->getMessage();
    }
  }
  public static function registerUser($username, $password, $mail)
  {
    try{
      $db = static::getDB();

      $sql = "INSERT INTO users (username, password, mail)
              VALUES (:username, :password, :mail)";
      $password = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':username', $username, PDO::PARAM_STR);
      $stmt->bindValue(':password', $password, PDO::PARAM_STR);
      $stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
      if($stmt->execute())
      {
        return true;
      }
      else
      {
        return false;
      }
    }
    catch(PDOExpection $e){
      echo $e->getMessage();
    }
  }

  public static function editUser($username, $password, $mail, $id)
  {
    try{
      $db = static::getDB();

      $sql = "UPDATE users SET username=:username, password=:password, mail=:mail
              WHERE id=:id";
      $password = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':username', $username, PDO::PARAM_STR);
      $stmt->bindValue(':password', $password, PDO::PARAM_STR);
      $stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);

      if($stmt->execute())
      {
        return true;
      }
      else
      {
        return false;
      }
    }
    catch(PDOExpection $e){
      echo $e->getMessage();
    }
  }
  public static function authenticate($mail, $password)
  {
    try
    {
      $db = static::getDB();

      $sql = "SELECT *
              FROM users
              WHERE mail=:mail";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':mail', $mail, PDO::PARAM_STR);

      $stmt->execute();
      if($user = $stmt->fetch())
      {
        if(password_verify($password, $user['password']))
        {
          return $user;
        }
      }
    }
    catch(PDOExpection $e)
    {
      echo $e->getMessage();
    }
  }
}
