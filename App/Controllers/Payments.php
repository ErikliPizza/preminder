<?php

namespace App\Controllers;
use \Core\View;
use \Core\Auth;
use App\Models\Payment;


class Payments extends \Core\Controller
{
  private $title;
  private $price;
  private $payment_day;
  public $errors = [];
  protected function before()
  {
    session_start();
    Auth::requireLogin();
    if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete']))
    {
      $this->title = $_POST['title'];
      $this->price = $_POST['price'];
      $this->payment_day = $_POST['payment_day'];
      if(!preg_match('/^[a-z][a-z ]*$/i', $this->title)) $this->errors[] = "invalid title";
      if(!preg_match('/^[0-9]+(?:\.[0-9]+)?$/', $this->price)) $this->errors[] = "invalid price";
      if(preg_match('/^\\d+$/', $this->payment_day))
      {
        if($this->payment_day < 1 || $this->payment_day > 31) $this->errors[] = "invalid day";
      }
      else $this->errors[] = "invalid day";
      if($this->errors)
      {
        View::renderTemplate('Payments/index.html', [
              'errors' => $this->errors
            ]);
        die();
      }

    }
  }

  protected function after()
  {
    static::redirect("/payments/index");
    die();
  }
  public function indexAction()
  {
    $paymentTable = Payment::getLogs($_SESSION['userid']);
    $contents = Payment::getAll($_SESSION['userid']);
    if($contents || $paymentTable)
    {
      View::renderTemplate('Payments/index.html', [
            'payments' => $contents,
            'paymentTable' => $paymentTable
          ]);
    }
    else
    {
      View::renderTemplate('Payments/index.html');
    }
    die();
  }
  public function addAction()
  {
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add']))
    {
      Payment::addPayment($this->title, $this->price, $this->payment_day, $_SESSION['userid']);
    }
  }

  public function updateAction()
  {
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update']))
    {
      Payment::updatePayment($this->title, $this->price, $this->payment_day, $_POST['content_id']);
    }
  }
  public function deleteAction()
  {
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']))
    {
      Payment::deletePayment($_POST['content_id']);
    }
  }


}
