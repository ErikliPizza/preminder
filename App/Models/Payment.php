<?php

namespace App\Models;
use PDO;

class Payment extends \Core\Model
{
  public static function getAll($author_id)
  {
    try{
      $db = static::getDB();
      $sql = "SELECT *, (SELECT sum(price) as 'totalPrice'
                         FROM payments where author_id=:author_id) AS totalPrice
                         FROM payments where author_id=:author_id;";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':author_id', $author_id, PDO::PARAM_INT);
      $stmt->execute();
      return $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(PDOExpection $e){
      echo $e->getMessage();
    }
  }
  
  public static function getLogs($author_id)
  {
    try{
      $db = static::getDB();
      $sql = "SELECT title, COUNT(*) as repeated, SUM(payed_price) as totalPayed,
              (SELECT sum(payed_price) as tp FROM logs where author_id=:author_id) as tp
              FROM logs where author_id = :author_id
              GROUP BY title;";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':author_id', $author_id, PDO::PARAM_INT);
      $stmt->execute();
      return $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(PDOExpection $e){
      echo $e->getMessage();
    }
  }


  public static function addPayment($title, $price, $payment_day, $author_id)
  {
    try{
      $db = static::getDB();

      $sql = "INSERT INTO payments (title, price, payment_day, author_id)
              VALUES (:title, :price, :payment_day, :author_id)";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':title', $title, PDO::PARAM_STR);
      $stmt->bindValue(':price', $price, PDO::PARAM_STR);
      $stmt->bindValue(':payment_day', $payment_day, PDO::PARAM_INT);
      $stmt->bindValue(':author_id', $author_id, PDO::PARAM_INT);
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

  public static function updatePayment($title, $price, $payment_day, $content_id)
  {
    try{
      $db = static::getDB();
      $sql = "UPDATE payments SET title=:title, price=:price, payment_day=:payment_day
              WHERE id=:content_id";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':title', $title, PDO::PARAM_STR);
      $stmt->bindValue(':price', $price, PDO::PARAM_STR);
      $stmt->bindValue(':payment_day', $payment_day, PDO::PARAM_INT);
      $stmt->bindValue(':content_id', $content_id, PDO::PARAM_INT);
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

  public static function deletePayment($content_id)
  {
    try{
      $db = static::getDB();
      $sql = "DELETE FROM payments
              WHERE id=:content_id";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':content_id', $content_id, PDO::PARAM_INT);
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
}
