<?php
ob_start();
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("./inc/database.php");
require_once("./inc/main.php");

class Page extends Database
{
  private $_secret_key;

  public function __construct()
  {
    $dbh = new Database;
    $main = new Main;

    $this->_secret_key = $_COOKIE['secret_key'];

    if(!empty($this->_secret_key)) {
      $this->_secret_key = mysqli_real_escape_string($dbh->connect(), $_COOKIE['secret_key']);
      $this->_secret_key = htmlspecialchars($this->_secret_key);
      $this->_secret_key = trim($this->_secret_key);
      
      $select_key = $dbh->connect()->query("SELECT uid FROM users WHERE secret_key='$this->_secret_key'");
      $check_key = $select_key->num_rows;

      if($check_key == 0 && $check_key <= 1) {
        header("Location: ".$main->link('module/dashboard'));
      } else {
        header("Location: ".$main->link('page/login'));
      }
    } else {
      header("Location: ".$main->link('page/login'));
    }
  }
}

$page = new Page;

ob_end_flush();