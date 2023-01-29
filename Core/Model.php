<?php
namespace Core;

use PDO;

abstract class Model
{

  protected static function getDB()
  {

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
    return $db;
  }
}
