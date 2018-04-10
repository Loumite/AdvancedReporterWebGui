<?php
error_reporting(E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

class Database
{
  private $_dbhost;
  private $_dbuser;
  private $_dbpass;
  private $_dbname;

  protected function connect()
  {
    $this->_dbhost = "localhost";
    $this->_dbuser = "root";
    $this->_dbpass = "";
    $this->_dbname = "reporter";

    $connect = new mysqli($this->_dbhost, $this->_dbuser, $this->_dbpass, $this->_dbname);
    mysqli_set_charset($connect, 'utf8mb4');

    if($connect->connect_error) {
      return die("<h3>Failed to connect to the database! Error: ".$connect->connect_error."</h3>");

      $connect->close();
    } else {
      return $connect;
    }
  }
}