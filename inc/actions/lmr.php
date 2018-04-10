<?php
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("../database.php");

class LoadMyReports extends Database
{
  private $_secret_key;
  private $_setting;
  private $_user;
  private $_table;
  private $_ign;
  private $_id;
  private $_ID;

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
  }

  private function setting($_setting)
  {
    $dbh = new Database;

    $this->_setting = $_setting;

    $select_setting = $dbh->connect()->query("SELECT value FROM settings WHERE name='$this->_setting'");
    $display_setting = $select_setting->fetch_array();

    return $display_setting['value'];
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

      $this->_table = $this->setting("table_name");
      $this->_ign = $this->user("ign");

      $select_reports = $dbh->connect()->query("SELECT * FROM $this->_table WHERE ticketManager='$this->_ign'");

      $this->raw_reports = array();
      $this->reports = array();

      while($display_reports = $select_reports->fetch_array())
      {
        $this->_id += 1;
        $this->_ID = $display_reports['ID'];

        $this->raw_reports[$this->_ID]['id'] = $this->_id;
        $this->raw_reports[$this->_ID]['reported'] = $display_reports['reported'];
        $this->raw_reports[$this->_ID]['reporter'] = $display_reports['reporter'];

        if($display_reports['open'] == 1)
        {
          $this->raw_reports[$this->_ID]['status'] = "<span class='badge badge-success'>Open</span>";
        }
        else
        {
          $this->raw_reports[$this->_ID]['status'] = "<span class='badge badge-danger'>Closed</span>";
        }

        $this->raw_reports[$this->_ID]['server'] = $display_reports['serverName'];
        $this->raw_reports[$this->_ID]['action'] = "<a href='".$this->setting("website_scheme").$this->setting("website_domain")."/module/report/".$display_reports['ID']."' class='btn btn-primary btn-sm'><i class='fas fa-eye'></i> View</a>";
      }

      foreach($this->raw_reports as $reports_2)
      {
        $this->reports[] = $reports_2;
      }

      echo json_encode($this->reports);
    }
  }
}

$lmr = new LoadMyReports;