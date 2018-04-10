<?php
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("../database.php");

class EditReport extends Database
{
  private $_secret_key;
  private $_id;
  private $_open;
  private $_resolving;
  private $_how_resolved;
  private $_setting;
  private $_table;
  private $_user;
  private $_tm;
  private $_r_reports;

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

  private function setting($_setting)
  {
    $dbh = new Database;

    $this->_setting = $_setting;

    $select_setting = $dbh->connect()->query("SELECT value FROM settings WHERE name='$this->_setting'");
    $display_setting = $select_setting->fetch_array();

    return $display_setting['value'];
  }

  private function checkKey() 
  {
    $dbh = new Database;

    if(!empty($_POST['id']))
    {
      $this->_table = $this->setting("table_name");

      $this->_id = mysqli_real_escape_string($dbh->connect(), $_POST['id']);
      $this->_id = htmlspecialchars($this->_id);
      $this->_id = trim($this->_id);

      $select_report = $dbh->connect()->query("SELECT reported FROM $this->_table WHERE ID='$this->_id'");
      $check_report = $select_report->num_rows;

      if($check_report == 1)
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

    $this->_secret_key = mysqli_real_escape_string($dbh->connect(), $_COOKIE['secret_key']);
    $this->_secret_key = htmlspecialchars($this->_secret_key);
    $this->_secret_key = trim($this->_secret_key);

    $this->_user = $_user;

    $select_user = $dbh->connect()->query("SELECT * FROM users WHERE secret_key='$this->_secret_key'");
    $display_user = $select_user->fetch_array();

    return $display_user[$this->_user];
  }

  public function __construct()
  {
    if($this->checkSession() == true)
    {
      $dbh = new Database;

      $this->error = false;

      if(isset($_POST['id']))
      {
        $this->_id = mysqli_real_escape_string($dbh->connect(), $_POST['id']);
        $this->_id = htmlspecialchars($this->_id);
        $this->_id = trim($this->_id);
        $this->_open = mysqli_real_escape_string($dbh->connect(), $_POST['open']);
        $this->_open = htmlspecialchars($this->_open);
        $this->_open = trim($this->_open);
        $this->_resolving = mysqli_real_escape_string($dbh->connect(), $_POST['resolving']);$this->_resolving = htmlspecialchars($this->_resolving);
        $this->_resolving = trim($this->_resolving);
        $this->_how_resolved = mysqli_real_escape_string($dbh->connect(), $_POST['how_resolved']);
        $this->_how_resolved = htmlspecialchars($this->_how_resolved);
        $this->_how_resolved = trim($this->_how_resolved);

        if(empty($this->_id))
        {
          $this->error = true;
          $this->errorName = "The id field is empty!";
        }
        else if(!is_numeric($this->_id))
        {
          $this->error = true;
          $this->errorName = "Invalid ID!";
        }

        if(empty($this->_open))
        {
          $this->error = true;
          $this->errorName = "The open field is empty!";
        }
        else if(!is_numeric($this->_open))
        {
          $this->error = true;
          $this->errorName = "Invalid open field!";
        }
        else
        {
          if($this->_open == 1)
          {
            $this->_open = 1;
          }
          else if($this->_open == 2)
          {
            $this->_open = 0;
          }
          else
          {
            $this->error = true;
            $this->errorName = "Invalid open field!";
          }
        }
        
        if(empty($this->_resolving))
        {
          $this->error = true;
          $this->errorName = "The resolving field is empty!";
        }
        else if(!is_numeric($this->_resolving))
        {
          $this->error = true;
          $this->errorName = "Invalid resolving field!";
        }
        else
        {
          if($this->_resolving == 1)
          {
            $this->_resolving = 1;
          }
          else if($this->_resolving == 2)
          {
            $this->_resolving = 0;
          }
          else
          {
            $this->error = true;
            $this->errorName = "Invalid resolving field!";
          }
        }

        if(empty($this->_how_resolved))
        {
          $this->error = true;
          $this->errorName = "Please enter how you resolved the report!";
        }
        else if(strlen($this->_how_resolved) < 4)
        {
          $this->error = true;
          $this->errorName = "The description is too short!";
        }
        else if(strlen($this->_how_resolved) > 200)
        {
          $this->error = true;
          $this->errorName = "The description is too large!";
        }

        if(!$this->error)
        {
          if($this->checkKey() == true)
          {
            $this->_table = $this->setting("table_name");
            $this->_tm = $this->user("ign");

            $update_report = $dbh->connect()->query("UPDATE $this->_table SET open='$this->_open', resolving='$this->_resolving', ticketManager='$this->_tm', howResolved='$this->_how_resolved' WHERE ID='$this->_id'");

            $explorde_rr = explode(',', $this->user("resolved_reports"));

            if(!in_array($this->_id, $explorde_rr))
            {
              if(!empty($explorde_rr[0]))
              {
                $this->_r_reports = $this->user("resolved_reports").",".$this->_id;
              }
              else
              {
                $this->_r_reports = $this->_id;
              }

              $update_user = $dbh->connect()->query("UPDATE users SET resolved_reports='$this->_r_reports' WHERE ign='$this->_tm'");
            }

            echo "<script type='text/javascript'>
            $(function() {
              toastr.options.preventDuplicates = true;
              toastr.options.closeButton = true;
              toastr.options.progressBar = true;

              toastr.options.onHidden = function() {
                window.location.href = '".$this->setting("website_scheme").$this->setting("website_domain")."/module/report/".$this->_id."';
              }

              toastr.success('Report Saved!', 'Success!');

              $('#er_modal').removeClass('show');
            });
            </script>";
          }
          else
          {
            echo "<script type='text/javascript'>
            $(function() {
              toastr.options.preventDuplicates = true;
              toastr.options.closeButton = true;
              toastr.options.progressBar = true;

              toastr.error('Report could not be found!', 'Failed!');
            });
            </script>";
          }
        }
        else
        {
          echo "<script type='text/javascript'>
          $(function() {
            toastr.options.preventDuplicates = true;
            toastr.options.closeButton = true;
            toastr.options.progressBar = true;

            toastr.error('".$this->errorName."', 'Failed!');
          });
          </script>";
        }
      }
    }
  }
}

$er = new EditReport;