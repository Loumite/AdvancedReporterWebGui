<?php
ob_start();
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("./database.php");

class RedirectToIndex extends Database
{
  private $_setting;

  private function setting($_setting)
  {
    $this->_setting = $_setting;

    $dbh = new Database;

    $select_setting = $dbh->connect()->query("SELECT value FROM settings WHERE name='$this->_setting'");
    $display_setting = $select_setting->fetch_array();

    return $display_setting['value'];
  }

  public function __construct()
  {
    header("Location: ".$this->setting("website_scheme").$this->setting("website_domain")."/page/index");
  }
}

$rti = new RedirectToIndex;

ob_end_flush();