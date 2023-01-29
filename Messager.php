<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__)); //.env source
$dotenv->load();
$mail = new PHPMailer(true);


$month = date("m");
$month = ltrim($month, "0");
$day = date("d");
$day = ltrim($day, "0");
$maxDay;


switch ($month)
{
  case 1:
    $maxDay=31;
    break;
  case 2:
    $maxDay=28;
    break;
  case 3:
    $maxDay=31;
    break;
  case 4:
    $maxDay=30;
    break;
  case 5:
    $maxDay=31;
    break;
  case 6:
    $maxDay=30;
    break;
  case 7:
    $maxDay=31;
    break;
  case 8:
    $maxDay=31;
    break;
  case 9:
    $maxDay=30;
    break;
  case 10:
    $maxDay=31;
    break;
  case 11:
    $maxDay=30;
    break;
  case 12:
    $maxDay=31;
    break;
  default:
    echo "error";
    return;
}

static $db = null;
if ($db === null) {

  $host = $_ENV["DB_HOST"];
  $dbname = $_ENV["DB_NAME"];
  $username = $_ENV["DB_USER"];
  $password = $_ENV["DB_PASS"];

    try {
        $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8",
                      $username, $password);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
  try{
    $sql = "SELECT *, users.mail, users.username, users.password FROM payments
            INNER JOIN users ON payments.author_id = users.id";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  catch(PDOExpection $e){
    echo $e->getMessage();
  }
  foreach ($results as $result)
  {

    if($result['payment_day']>$maxDay)
    {
      if($day == $maxDay)
      {
        $result['payment_day'] = 0;
      }
    }
    if($result['payment_day'] == $day || $result['payment_day'] == 0)
    {


      $sql = "INSERT INTO logs (author_id, title, payed_price)
              VALUES (:author_id, :title, :payed_price)";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':author_id', $result['author_id'], PDO::PARAM_INT);
      $stmt->bindValue(':title', $result['title'], PDO::PARAM_STR);
      $stmt->bindValue(':payed_price', $result['price'], PDO::PARAM_STR);
      if($stmt->execute())
      {

        $lastId = $db->lastInsertId();
        $deleteToken = 'https://preminder.noircontact.tech/deleter.php?m='.$result['mail'].'&p='.$result['password'].'&i='.$lastId;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'test@gmail.com';
        $mail->Password = 'test_password';
        $mail->SMTPSecure = 'ssl';
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);
        $mail->Port = 465;

        $mail->setFrom('no-reply@test');
        $mail->addAddress($result['mail']);
        $mail->Subject = 'HATIRLATICI: '.$result['title'];
        $mail->Body = '<p style="text-align: center; font-size: 1.5em;">Merhaba '.$result['username'].'! Bugün '.strtoupper($result['title']).' için: '.$result['price'].'&#x20BA; tutarında ödeme yapacağını duyduk.</p> <p style="text-align:center; font-size: 1em;">Saygılar: Plummyw Ekibi - https://preminder.noircontact.tech/<p>'.'<p style="text-align:center; font-size: 1em; font-style: italic;">Bu ödemeyi yapmayacaksanız, ilgili bağlantıya tıklayarak ödeme geçmişinizden silebilirsiniz: '.$deleteToken;

        if($mail->send())
        {
          $mail->ClearAddresses();
          echo "başarılı: ".$result['username']." - ".$result['mail'];
        }
      }
    }
  }
