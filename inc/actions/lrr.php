<?php
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("../database.php");

class Loadrr extends Database
{
  private $_setting;
  private $_secret_key;
  private $_table;

  private function setting($_setting)
  {
    $dbh = new Database;

    $this->_setting = $_setting;

    $select_setting = $dbh->connect()->query("SELECT value FROM settings WHERE name='$this->_setting'");
    $display_setting = $select_setting->fetch_array();

    return $display_setting['value'];
  }

  private function checkSession()
  {
    $dbh = new Database;

    $this->_secret_key = $_COOKIE['secret_key'];

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

  public function __construct()
  {
    if($this->checkSession() == true)
    {
      $dbh = new Database;

      $this->_table = $this->setting("table_name");

      $select_reports = $dbh->connect()->query("SELECT * FROM $this->_table WHERE open='0' AND resolving='0'");

      $raw_reports = array();
      $reports = array();

      while($display_reports = $select_reports->fetch_array())
      {
        $this->report_id += 1;
        $this->report_ID = $display_reports['ID'];

        $raw_reports[$this->report_ID]['id'] = $this->report_id;
        $raw_reports[$this->report_ID]['reported'] = $display_reports['reported'];
        $raw_reports[$this->report_ID]['reporter'] = $display_reports['reporter'];
        
        if($display_reports['open'] == 1)
        {
          $raw_reports[$this->report_ID]['status'] = "<span class='badge badge-success'>Open</span>";
        }
        else
        {
          $raw_reports[$this->report_ID]['status'] = "<span class='badge badge-danger'>Closed</span>";
        }

        $raw_reports[$this->report_ID]['section'] = $display_reports['section'];
        $raw_reports[$this->report_ID]['serverName'] = $display_reports['serverName'];
        $raw_reports[$this->report_ID]['ticketManager'] = $display_reports['ticketManager'];
      }

      foreach($raw_reports as $reports_2)
      {
        $reports[] = $reports_2;
      }

      echo json_encode($reports);
    }
    else
    {
      echo "<script type='text/javascript'>
      $(function() {
        window.location.href = '".$this->setting("website_scheme").$this->setting("website_domain")."/page/login';
      });
      </script>";
    }
  }
}

$lrr = new Loadrr;