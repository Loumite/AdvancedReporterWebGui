<?php
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("../database.php");

class TakeReport extends Database
{
  private $_setting;
  private $_secret_key;
  private $_id;
  private $_table;
  private $_ign;
  private $_user;

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

  private function user($_user)
  {
    $dbh = new Database;
    
    $this->_user = $_user;

    $this->_secret_key = mysqli_real_escape_string($dbh->connect(), $_COOKIE['secret_key']);
    $this->_secret_key = htmlspecialchars($this->_secret_key);
    $this->_secret_key = trim($this->_secret_key);

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

        if(!$this->error)
        {
          $this->_table = $this->setting("table_name");

          $select_report = $dbh->connect()->query("SELECT ticketManager,open,resolving FROM $this->_table WHERE ID='$this->_id'");
          $check_report = $select_report->num_rows;

          if($check_report == 1)
          {
            $display_report = $select_report->fetch_array();

            if($display_report['open'] == 1 && $display_report['ticketManager'] == "none")
            {
              $this->_ign = $this->user("ign");

              $update_report = $dbh->connect()->query("UPDATE $this->_table SET resolving='1',open='0', ticketManager='$this->_ign' WHERE ID='$this->_id'");

              echo "<script type='text/javascript'>
              $(function() {
                toastr.options.preventDuplicates = true;
                toastr.options.closeButton = true;
                toastr.options.progressBar = true;

                toastr.options.onHidden = function() {
                  window.location.href = '".$this->setting("website_scheme").$this->setting("website_domain")."/module/report/".$this->_id."';
                }

                toastr.success('You will be redirected in a few seconds!', 'Success!');
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

                toastr.error('This report is already taken!', 'Failed!');
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

$tr = new TakeReport;