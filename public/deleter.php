<?php
if(isset($_GET['m']) && isset($_GET['p']) && isset($_GET['i']))
{
  $mail = $_GET['m'];
  $pw = $_GET['p'];
  $id = $_GET['i'];
}
else
{
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
    {
      $protocol = 'https';
    }
    else
    {
      $protocol = 'http';
    }
    header("Location: $protocol://" . $_SERVER['HTTP_HOST'] . '/');
    exit;
}
static $db = null;
if ($db === null) {

  require '../vendor/autoload.php';
  $dotenv = Dotenv\Dotenv::createImmutable("../"); //.env source belirle
  $dotenv->load();
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

  $sql = "SELECT id FROM users
          WHERE mail = :mail and password = :password";
  $stmt = $db->prepare($sql);
  $stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
  $stmt->bindValue(':password', $pw, PDO::PARAM_STR);

  $stmt->execute();
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
  if($stmt->rowCount() > 0)
  {
    $sql = "DELETE FROM logs
            WHERE id=:id and author_id=:author_id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':author_id', $results[0]['id'], PDO::PARAM_INT);

    $stmt->execute();
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
    {
      $protocol = 'https';
    }
    else
    {
      $protocol = 'http';
    }
    header("Location: $protocol://" . $_SERVER['HTTP_HOST'] . '/');
    exit;
  }
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
    {
      $protocol = 'https';
    }
    else
    {
      $protocol = 'http';
    }
    header("Location: $protocol://" . $_SERVER['HTTP_HOST'] . '/');
    exit;
