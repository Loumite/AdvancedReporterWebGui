<?php
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("./inc/database.php");

class Main extends Database
{
  private $_setting;
  private $_link;

  public function setting($_setting)
  {
    $this->_setting = $_setting;

    $dbh = new Database;

    $select_setting = $dbh->connect()->query("SELECT value FROM settings WHERE name='$this->_setting'");
    $display_setting = $select_setting->fetch_array();

    return $display_setting['value'];
  }

  public function link($_link)
  {
    $this->_link = $_link;

    if(empty($this->_link))
    {
      return $this->setting("website_scheme").$this->setting("website_domain");
    } 
    else
    {
      return $this->setting("website_scheme").$this->setting("website_domain")."/".$this->_link;
    }
  }
}