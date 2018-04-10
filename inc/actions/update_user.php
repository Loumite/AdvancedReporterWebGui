<?php
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("../database.php");
require_once("../pw_encrypter.php");

class UpdateUser extends Database
{
  private $_secret_key;
  private $_setting;
  private $_user;
  private $_username;
  private $_ign;
  private $_old_ign;
  private $_table;
  private $_email;
  private $_avatar;
  private $_password;
  private $_new_password;

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
    if($this->checkSession() == true)
    {
      $dbh = new Database;
      $pw = new PasswordEncrypter;

      $this->error = false;

      if(isset($_POST['submit']))
      {
        $this->_username = mysqli_real_escape_string($dbh->connect(), $_POST['username']);
        $this->_username = htmlspecialchars($this->_username);
        $this->_username = trim($this->_username);
        $this->_ign = mysqli_real_escape_string($dbh->connect(), $_POST['ign']);
        $this->_ign = htmlspecialchars($this->_ign);
        $this->_ign = trim($this->_ign);
        $this->_email = mysqli_real_escape_string($dbh->connect(), $_POST['email']);
        $this->_email = htmlspecialchars($this->_email);
        $this->_email = trim($this->_email);
        $this->_avatar = mysqli_real_escape_string($dbh->connect(), $_POST['avatar']);
        $this->_avatar = htmlspecialchars($this->_avatar);
        $this->_avatar = trim($this->_avatar);
        $this->_password = mysqli_real_escape_string($dbh->connect(), $_POST['password']);
        $this->_password = htmlspecialchars($this->_password);
        $this->_password = trim($this->_password);

        if(empty($this->_username))
        {
          $this->error = true;
          $this->errorName = "Please enter your username!";
        }
        else if(strlen($this->_username) > 128)
        {
          $this->error = true;
          $this->errorName = "The username is too long!";
        }
        else if(strlen($this->_username) < 4)
        {
          $this->error = true;
          $this->errorName = "The username is too short!";
        }

        if(empty($this->_ign))
        {
          $this->error = true;
          $this->errorName = "Please enter your IGN!";
        }
        else if(strlen($this->_ign) > 128)
        {
          $this->error = true;
          $this->errorName = "The IGN is too long!";
        }
        else if(strlen($this->_ign) < 4)
        {
          $this->error = true;
          $this->errorName = "The IGN is too short!";
        }
        else if(!preg_match("/^[a-zA-Z0-9_]+$/", $this->_ign))
        {
          $this->error = true;
          $this->errorName = "The IGN contains illegal characters!";
        }

        if(empty($this->_email))
        {
          $this->error = true;
          $this->errorName = "Please enter your email address!";
        }
        else if(strlen($this->_email) > 200)
        {
          $this->error = true;
          $this->errorName = "The email address is too long!";
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

        if(empty($this->_avatar))
        {
          $this->error = true;
          $this->errorName = "Please enter your avatar link!";
        }
        else
        {
          $this->avatar_types = array('default', 'mc');

          if(!in_array($this->_avatar, $this->avatar_types))
          {
            if(!filter_var($this->_avatar, FILTER_VALIDATE_URL))
            {
              $this->error = true;
              $this->errorName = "Invalid link!";
            }
          }
        }

        if(!empty($this->_password))
        {
          if(strlen($this->_password) > 200)
          {
            $this->error = true;
            $this->errorName = "The password is too long!";
          }
          else if(strlen($this->_password) < 6)
          {
            $this->error = true;
            $this->errorName = "The password is too short!";
          }
        }

        if(!$this->error)
        {
          $this->_secret_key = mysqli_real_escape_string($dbh->connect(), $_COOKIE['secret_key']);
          $this->_secret_key = htmlspecialchars($this->_secret_key);
          $this->_secret_key = trim($this->_secret_key);

          if(empty($this->_password))
          {
            if($this->_ign == $this->user("ign"))
            {
              $update_user = $dbh->connect()->query("UPDATE users SET username='$this->_username', ign='$this->_ign', email='$this->_email', avatar='$this->_avatar' WHERE secret_key='$this->_secret_key'");

              echo "<script type='text/javascript'>
              $(function() {
                toastr.options.preventDuplicates = true;
                toastr.options.closeButton = true;
                toastr.options.progressBar = true;

                toastr.options.onHidden = function() {
                  window.location.href = '';
                }

                toastr.success('Changes Saved!', 'Success!');
              });
              </script>";
            }
            else
            {
              $this->_table = $this->setting("table_name");
              $this->_old_ign = $this->user("ign");

              $update_reports = $dbh->connect()->query("UPDATE $this->_table SET ticketManager='$this->_ign' WHERE ticketManager='$this->_old_ign'");

              $update_user = $dbh->connect()->query("UPDATE users SET username='$this->_username', ign='$this->_ign', email='$this->_email', avatar='$this->_avatar' WHERE secret_key='$this->_secret_key'");

              echo "<script type='text/javascript'>
              $(function() {
                toastr.options.preventDuplicates = true;
                toastr.options.closeButton = true;
                toastr.options.progressBar = true;

                toastr.options.onHidden = function() {
                  window.location.href = '';
                }

                toastr.success('Changes Saved!', 'Success!');
              });
              </script>";
            }
          }
          else
          {
            $this->_new_password = $pw->encryptPassword($this->_password);

            if($this->_ign == $this->user("ign"))
            {
              $update_user = $dbh->connect()->query("UPDATE users SET username='$this->_username', ign='$this->_ign', email='$this->_email', avatar='$this->_avatar', password='$this->_new_password' WHERE secret_key='$this->_secret_key'");

              echo "<script type='text/javascript'>
              $(function() {
                toastr.options.preventDuplicates = true;
                toastr.options.closeButton = true;
                toastr.options.progressBar = true;

                toastr.options.onHidden = function() {
                  window.location.href = '';
                }

                toastr.success('Changes Saved!', 'Success!');
              });
              </script>";
            }
            else
            {
              $this->_table = $this->setting("table_name");
              $this->_old_ign = $this->user("ign");

              $update_reports = $dbh->connect()->query("UPDATE $this->_table SET ticketManager='$this->_ign' WHERE ticketManager='$this->_old_ign'");

              $update_user = $dbh->connect()->query("UPDATE users SET username='$this->_username', ign='$this->_ign', email='$this->_email', avatar='$this->_avatar', password='$this->_new_password' WHERE secret_key='$this->_secret_key'");

              echo "<script type='text/javascript'>
              $(function() {
                toastr.options.preventDuplicates = true;
                toastr.options.closeButton = true;
                toastr.options.progressBar = true;

                toastr.options.onHidden = function() {
                  window.location.href = '';
                }

                toastr.success('Changes Saved!', 'Success!');
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

            toastr.error('".$this->errorName."', 'Failed!');
          });
          </script>";
        }
      }
    }
  }
}

$uu = new UpdateUser;