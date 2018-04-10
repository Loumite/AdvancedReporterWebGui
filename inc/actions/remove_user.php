<?php
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("../database.php");

class RemoveUser extends Database
{
  private $_secret_key;
  private $_email;

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

  private function checkUser()
  {
    $dbh = new Database;

    if($this->checkSession() == true)
    {
      $this->_secret_key = mysqli_real_escape_string($dbh->connect(), $_COOKIE['secret_key']);
      $this->_secret_key = htmlspecialchars($this->_secret_key);
      $this->_secret_key = trim($this->_secret_key);

      $select_user = $dbh->connect()->query("SELECT cc_users FROM users WHERE secret_key='$this->_secret_key'");
      $display_user = $select_user->fetch_array();

      if($display_user['cc_users'] == 1)
      {
        return true;
      }
      else
      {
        return false;
      }
    }  
  }

  public function __construct()
  {
    if($this->checkSession() == true)
    {
      if($this->checkUser() == true)
      {
        $dbh = new Database;

        $this->error = false;

        if(isset($_POST['submit']))
        {
          $this->_email = mysqli_real_escape_string($dbh->connect(), $_POST['email']);
          $this->_email = htmlspecialchars($this->_email);
          $this->_email = trim($this->_email);

          if(empty($this->_email))
          {
            $this->error = true;
            $this->errorName = "Please enter an email address!";
          }
          else if(strlen($this->_email) > 128)
          {
            $this->error = true;
            $this->errorName = "The email address is too large!";
          }
          else if(strlen($this->_email) < 4)
          {
            $this->error = true;
            $this->errorName = "The email address is too short!";
          }
          else if(!filter_var($this->_email, FILTER_VALIDATE_EMAIL))
          {
            $this->error = true;
            $this->errorName = "Invalid email address!";
          }

          if(!$this->error)
          {
            $select_user = $dbh->connect()->query("SELECT * FROM users WHERE email='$this->_email'");
            $check_user = $select_user->num_rows;

            if($check_user != 0)
            {
              $display_user = $select_user->fetch_array();

              $this->_secret_key = mysqli_real_escape_string($dbh->connect(), $_COOKIE['secret_key']);
              $this->_secret_key = htmlspecialchars($this->_secret_key);
              $this->_secret_key = trim($this->_secret_key);

              if($display_user['secret_key'] != $this->_secret_key)
              {
                $remove_user = $dbh->connect()->query("DELETE FROM users WHERE email='$this->_email'");

                echo "<script type='text/javascript'>
                $(function() {
                  toastr.options.preventDuplicates = true;
                  toastr.options.closeButton = true;
                  toastr.options.progressBar = true;

                  toastr.options.onHidden = function() {
                    window.location.href = '';
                  }

                  toastr.success('User removed!', 'Success!');
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

                  toastr.error('You can not remove yourself!', 'Failed!');
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

                toastr.error('User could not be found!', 'Failed!');
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
}

$ru = new RemoveUser;