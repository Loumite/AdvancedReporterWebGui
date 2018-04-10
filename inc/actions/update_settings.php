<?php
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("../database.php");

class UpdateSettings extends Database
{
  private $_secret_key;
  private $_user;
  private $_setting_name;
  private $_value;

  private function checkSession()
  {
    $dbh = new Database;

    $this->_secret_key = $_COOKIE["secret_key"];

    if(!empty($this->_secret_key))
    {
      $this->_secret_key = mysqli_real_escape_string($dbh->connect(), $_COOKIE['secret_key']);
      $this->_secret_key = htmlspecialchars($this->_secret_key);
      $this->_secret_key = trim($this->_secret_key);

      $select_key = $dbh->connect()->query("SELECT uid FROM users WHERE secret_key='$this->_secret_key'");
      $check_key = $select_key->num_rows;

      if($check_key == 1)
      {
        return true;
      }
      else
      {
        return false;
      }
    }
    else
    {
      return false;
    }
  }

  private function user($_user)
  {
    $dbh = new Database;

    $this->_user = $_user;

    if($this->checkSession() == true)
    {
      $this->_secret_key = mysqli_real_escape_string($dbh->connect(), $_COOKIE['secret_key']);
      $this->_secret_key = htmlspecialchars($this->_secret_key);
      $this->_secret_key = trim($this->_secret_key);

      $select_user = $dbh->connect()->query("SELECT * FROM users WHERE secret_key='$this->_secret_key'");
      $display_user = $select_user->fetch_array();

      return $display_user[$this->_user];
    }
  }

  public function __construct()
  {
    if($this->checkSession() == true)
    {
      $dbh = new Database;

      if($this->user("is_admin") == 1)
      {
        $select_settings = $dbh->connect()->query("SELECT * FROM settings");
      
        while($display_settings = $select_settings->fetch_array())
        {
          $this->_setting_name = $display_settings['name'];
          $this->_value = $_POST[$this->_setting_name];

          if($display_settings['value'] != $this->_value)
          {
            $update_settings = $dbh->connect()->query("UPDATE settings SET value='$this->_value' WHERE name='$this->_setting_name'");
          }
        }

        echo "<script type='text/javascript'>
        $(function() {
          toastr.options.preventDuplicates = true;
          toastr.options.closeButton = true;
          toastr.options.progressBar = true;

          toastr.success('Settings Saved!', 'Success!');
        });
        </script>";
      }
    }
  }
}

$us = new UpdateSettings;