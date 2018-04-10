<?php
ob_start();
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("./inc/database.php");

class PageLoader extends Database
{
  private $_page;
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
    $this->_page = mb_strtolower($_GET['page']);
    
    if(!empty($this->_page))
    {
      if(file_exists("./pages/".$this->_page.".php"))
      {
        require_once("./pages/".$this->_page.".php");
      }
      else
      {
        header("Location: ".$this->setting("website_scheme").$this->setting("website_domain")."/page/error");
      }
    }
    else
    {
      header("Location: ".$this->setting("website_scheme").$this->setting("website_domain")."/page/error");
    }
  }
}

$page_loader = new PageLoader;

ob_end_flush();