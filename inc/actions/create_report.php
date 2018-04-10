<?php
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("../database.php");
require_once("../rs_generator.php");

class CreateReport extends Database
{
  private $_setting;
  private $_table;
  private $_time;
  private $_string;
  private $_reported;
  private $_reporter;
  private $_reason;
  private $_ID;

  private function setting($_setting)
  {
    $dbh = new Database;

    $this->_setting = $_setting;

    $select_setting = $dbh->connect()->query("SELECT value FROM settings WHERE name='$this->_setting'");
    $display_setting = $select_setting->fetch_array();

    return $display_setting['value'];
  }

  private function checkTime()
  {
    $this->_time = $_COOKIE['time'];

    if(!empty($this->_time))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  public function __construct()
  {
    if($this->checkTime() == false)
    {
      $dbh = new Database;
      $rs = new RandomStringGenerator;

      $this->error = false;

      if(isset($_POST['submit']))
      {
        $this->_reported = mysqli_real_escape_string($dbh->connect(), $_POST['reported']);
        $this->_reported = htmlspecialchars($this->_reported);
        $this->_reported = trim($this->_reported);
        $this->_reporter = mysqli_real_escape_string($dbh->connect(), $_POST['reporter']);
        $this->_reporter = htmlspecialchars($this->_reporter);
        $this->_reporter = trim($this->_reporter);
        $this->_reason = mysqli_real_escape_string($dbh->connect(), $_POST['reason']);
        $this->_reason = htmlspecialchars($this->_reason);
        $this->_reason = trim($this->_reason);

        if(empty($this->_reported))
        {
          $this->error = true;
          $this->errorName = "Please enter the reported persons name!";
        }
        else if(strlen($this->_reported) < 4)
        {
          $this->error = true;
          $this->errorName = "The reporter field is too short!";
        }
        else if(strlen($this->_reported) > 50)
        {
          $this->error = true;
          $this->errorName = "The reporter field is too long!";
        }
        else if(!preg_match("/^[a-zA-Z0-9_]+$/", $this->_reported))
        {
          $this->error = true;
          $this->errorName = "The reporter field contains illegal characters!";
        }

        if(empty($this->_reporter))
        {
          $this->error = true;
          $this->errorName = "Please enter your name!";
        }
        else if(strlen($this->_reporter) < 4)
        {
          $this->error = true;
          $this->errorName = "Your name is too short!";
        }
        else if(strlen($this->_reporter) > 50)
        {
          $this->error = true;
          $this->errorName = "Your name is too long!";
        }
        else if(!preg_match("/^[a-zA-Z0-9_]+$/", $this->_reporter))
        {
          $this->error = true;
          $this->errorName = "Your name contains illegal characters!";
        }

        if(empty($this->_reason))
        {
          $this->error = true;
          $this->errorName = "Please enter a reason!";
        }
        else if(strlen($this->_reason) < 6)
        {
          $this->error = true;
          $this->errorName = "The reason is too short!";
        }
        else if(strlen($this->_reason) > 200)
        {
          $this->error = true;
          $this->errorName = "The reason is too long!";
        }

        if(!$this->error)
        {
          $this->_table = $this->setting("table_name");
          $this->_string = $rs->randomStringGenerate(8);

          $select_reports = $dbh->connect()->query("SELECT reported FROM $this->_table");
          $count_reports = $select_reports->num_rows;

          $this->_ID = $count_reports + 1;

          $create_report = $dbh->connect()->query("INSERT INTO $this->_table (ID, reported, reporter, reason, world, x, y, z, section, subSection, resolving, open, ticketManager, howResolved, serverName) VALUES ('".$this->_ID."', '".$this->_reported."', '".$this->_reporter."', '".$this->_reason."', 'none', '0', '0', '0', 'web', 'none', '0', '1', 'none', 'none', 'web')");

          if($_SERVER['HTTP_HOST'] == "localhost")
          {
            setcookie("time", $this->_string, false, "/", false);
          }
          else
          {
            setcookie("time", $this->_string, time()+1800);
          }

          echo "<script type='text/javascript'>
          $(function() {
            toastr.options.preventDuplicates = true;
            toastr.options.closeButton = true;
            toastr.options.progressBar = true;

            $('#sr-form input').val('');
            $('#sr-form textarea').val('');
            $('#cw').text('0');

            toastr.success('Report Submitted!', 'Success!');
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

            toastr.error('".$this->errorName."', 'Failed!');
          });
          </script>";
        }
      }
    }
    else
    {
      echo "<script type='text/javascript'>
      $(function() {
        toastr.options.preventDuplicates = true;
        toastr.options.closeButton = true;
        toastr.options.progressBar = true;

        toastr.error('Sorry but now you can not submit this report!', 'Failed!');
      });
      </script>";
    }
  }
}

$cr = new CreateReport;