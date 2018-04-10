<?php
ob_start();
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("./inc/database.php");

class ModuleLoader extends Database
{
  private $_module;
  private $_setting;

  private function setting($_setting)
  {
    $dbh = new Database;

    $this->_setting = $_setting;

    $select_setting = $dbh->connect()->query("SELECT value FROM settings WHERE name='$this->_setting'");
    $display_setting = $select_setting->fetch_array();

    return $display_setting['value'];
  }

  public function __construct()
  {
    $this->_module = $_GET['module'];

    if(!empty($this->_module))
    {
      if(file_exists("./modules/".$this->_module.".php"))
      {
        require_once("./modules/".$this->_module.".php");
      }
      else
      {
        header("Location: ".$this->setting("website_scheme").$this->setting("website_domain")."/page/error");
      }
    }
    else
    {
      header("Location: ".$this->setting("website_scheme").$this->setting("website_domain")."/module/dashboard");
    }
  }
}

$module_loader = new ModuleLoader;

ob_end_flush();