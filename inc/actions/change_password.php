<?php
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("../database.php");
require_once("../rs_generator.php");
require_once("../pw_encrypter.php");

class ChangePassword extends Database
{
  private $_setting;
  private $_secret_key;
  private $_key;
  private $_raw_password;
  private $_password;
  private $_email;

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

  private function checkMailer()
  {
    if($this->setting("enable_mailer") == 1)
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
    if($this->checkMailer() == true)
    {
      if($this->checkSession() == false)
      {
        $this->error = false;

        if(isset($_POST['submit']))
        {
          $dbh = new Database;

          $this->_key = mysqli_real_escape_string($dbh->connect(), $_POST['key']);
          $this->_key = htmlspecialchars($this->_key);
          $this->_key = trim($this->_key);

          if(empty($this->_key))
          {
            $this->error = true;
            $this->errorName = "Please enter the key!";
          }
          else if(strlen($this->_key) > 180)
          {
            $this->error = true;
            $this->errorName = "The key is too long!";
          }
          else if(strlen($this->_key) < 6)
          {
            $this->error = true;
            $this->errorName = "The key is too short!";
          }

          if(!$this->error)
          {
            $select_key = $dbh->connect()->query("SELECT email FROM pw_reminder WHERE pwr_key='pwr_$this->_key'");
            $check_key = $select_key->num_rows;

            if($check_key == 1)
            {
              $display_key = $select_key->fetch_array();

              $this->_email = $display_key['email'];

              $rs = new RandomStringGenerator;
              $pw = new PasswordEncrypter;

              $this->_raw_password = $rs->randomStringGenerate(8);
              $this->_password = $pw->encryptPassword($this->_raw_password);

              $change_password = $dbh->connect()->query("UPDATE users SET password='$this->_password' WHERE email='$this->_email'");
              $remove_pwr = $dbh->connect()->query("DELETE FROM pw_reminder WHERE pwr_key='pwr_$this->_key'");

              echo "<script type='text/javascript'>
              $(function() {
                toastr.options.preventDuplicates = true;
                toastr.options.closeButton = true;
                toastr.options.progressBar = true;

                toastr.success('Password Changed!', 'Success!');

                $('#change-pw-form input.form-control').val('".$this->_raw_password."');
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

                toastr.error('Invalid key!', 'Failed!');
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
      else
      {
        echo "<script type='text/javascript'>
        $(function() {
          window.location.href = '".$this->setting("website_scheme").$this->setting("website_domain")."/module/dashboard';
        });
        </script>";
      }
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

$cpw = new ChangePassword;