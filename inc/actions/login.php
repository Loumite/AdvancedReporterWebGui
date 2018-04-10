<?php
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("../database.php");
require_once("../rs_generator.php");

class Login extends Database
{
  private $_email;
  private $_password;
  private $_checkbox;
  private $_secret_key;
  private $_new_secret_key;
  private $_setting;

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

      if($check_key != 0 && $check_key <= 1)
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
    $this->_setting = $_setting;

    $dbh = new Database;
    
    $select_setting = $dbh->connect()->query("SELECT value FROM settings WHERE name='$this->_setting'");
    $display_setting = $select_setting->fetch_array();

    return $display_setting['value'];
  }

  public function __construct()
  {
    if($this->checkSession() == false)
    {
      $dbh = new Database;

      $this->error = false;

      if(isset($_POST['submit']))
      {
        $this->_email = mysqli_real_escape_string($dbh->connect(), $_POST['email']);
        $this->_email = htmlspecialchars($this->_email);
        $this->_email = trim($this->_email);
        $this->_password = mysqli_real_escape_string($dbh->connect(), $_POST['password']);
        $this->_password = htmlspecialchars($this->_password);
        $this->_password = trim($this->_password);

        if(empty($this->_email))
        {
          $this->error = true;
          $this->errorName = "Please enter your email address!";
        }
        else if(strlen($this->_email) > 200)
        {
          $this->error = true;
          $this->errorName = "Maximum lenght reached!";
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

        if(empty($this->_password))
        {
          $this->error = true;
          $this->errorName = "Please enter your password!";
        }
        else if(strlen($this->_password) < 6)
        {
          $this->error = true;
          $this->errorName = "The password is too short!";
        }

        if(!$this->error)
        {
          $select_user = $dbh->connect()->query("SELECT password,secret_key FROM users WHERE email='$this->_email'");
          $check_user = $select_user->num_rows;

          if($check_user != 0)
          {
            $display_user = $select_user->fetch_array();
            $explode = explode('$', $display_user['password']);

            $hash_pw = hash("sha256", hash("sha256", $this->_password).$explode[2]);

            if($hash_pw == $explode[3])
            {
              if($_SERVER['HTTP_HOST'] == "localhost")
              {
                $rs = new RandomStringGenerator;

                $this->_new_secret_key = hash("sha256", $rs->randomStringGenerate(16));

                $update_secret_key = $dbh->connect()->query("UPDATE users SET secret_key='$this->_new_secret_key' WHERE email='$this->_email'");

                setcookie("secret_key", $this->_new_secret_key, false, "/", false);

                echo "<script type='text/javascript'>
                $(function() {
                  toastr.options.preventDuplicates = true;
                  toastr.options.closeButton = true;
                  toastr.options.progressBar = true;

                  $('#login-form input.form-control').val('');

                  toastr.options.onHidden = function() {
                    window.location.href = '".$this->setting("website_scheme").$this->setting("website_domain")."/module/dashboard';
                  }

                  toastr.success('You will be redirected in a few seconds!', 'Success!');
                });
                </script>";
              }
              else
              {
                $rs = new RandomStringGenerator;

                $this->_new_secret_key = hash("sha256", $rs->randomStringGenerate(16));
                $this->_checkbox = $_POST['checkbox'];

                $update_secret_key = $dbh->connect()->query("UPDATE users SET secret_key='$this->_new_secret_key' WHERE email='$this->_email'");

                if($this->_checkbox == true)
                {
                  setcookie("secret_key", $this->_new_secret_key, 2147483647);
                }
                else
                {
                  setcookie("secret_key", $this->_new_secret_key, time()+$this->seting("session_timeout"));
                }

                echo "<script type='text/javascript'>
                $(function() {
                  toastr.options.preventDuplicates = true;
                  toastr.options.closeButton = true;
                  toastr.options.progressBar = true;

                  $('#login-form input.form-control').val('');

                  toastr.options.onHidden = function() {
                    window.location.href = '".$this->setting("website_scheme").$this->setting("website_domain")."/module/dashboard';
                  }

                  toastr.success('You will be redirected in a few seconds!', 'Success!');
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

                toastr.error('Incorrect password!', 'Failed!');
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

              $('#login-form input.form-control').val('');
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
    else if($this->checkSession() == true)
    {
      echo "<script type='text/javascript'>
      $(function() {
        window.location.href = '".$this->setting("website_scheme").$this->setting("website_domain")."/module/dashboard';
      });
      </script>";
    }
  }
}

$login = new Login;