<?php
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("../database.php");
require_once("../id_generator.php");
require_once("../pw_encrypter.php");
require_once("../rs_generator.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once("../phpmailer/Exception.php");
require_once("../phpmailer/PHPMailer.php");
require_once("../phpmailer/SMTP.php");

class CreateUser extends Database
{
  private $_secret_key;
  private $_setting;
  private $_username;
  private $_ign;
  private $_email;
  private $_is_admin;
  private $_cc_users;
  private $_uid;
  private $_password;
  private $_raw_password;
  private $_date;

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
      if($this->checkUser() == true)
      {
        $dbh = new Database;

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
          $this->_is_admin = mysqli_real_escape_string($dbh->connect(), $_POST['is_admin']);
          $this->_is_admin = htmlspecialchars($this->_is_admin);
          $this->_is_admin = trim($this->_is_admin);
          $this->_cc_users = mysqli_real_escape_string($dbh->connect(), $_POST['cc_users']);
          $this->_cc_users = htmlspecialchars($this->_cc_users);
          $this->_cc_users = trim($this->_cc_users);

          if(empty($this->_username))
          {
            $this->error = true;
            $this->errorName = "Please enter a username!";
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
          else
          {
            $select_username = $dbh->connect()->query("SELECT uid FROM users WHERE username='$this->_username'");
            $check_username = $select_username->num_rows;

            if($check_username != 0)
            {
              $this->error = true;
              $this->errorName = "This username is already taken!";
            }
          }

          if(empty($this->_ign))
          {
            $this->error = true;
            $this->errorName = "Please enter an IGN!";
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
            $this->errorName = "The IGN contains illegal charactes!";
          }
          else
          {
            $select_ign = $dbh->connect()->query("SELECT uid FROM users WHERE ign='$this->_ign'");
            $check_ign = $select_ign->num_rows;

            if($check_ign != 0)
            {
              $this->error = true;
              $this->errorName = "This IGN is already taken!";
            }
          }

          if(empty($this->_email))
          {
            $this->error = true;
            $this->errorName = "Please enter an email address!";
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
          else
          {
            $select_email = $dbh->connect()->query("SELECT uid FROM users WHERE email='$this->_email'");
            $check_email = $select_email->num_rows;

            if($check_email != 0)
            {
              $this->error = true;
              $this->errorName = "This email address is already taken!";
            }
          }

          if(!is_numeric($this->_is_admin))
          {
            $this->error = true;
            $this->errorName = "The is_admin field is invalid!";
          }
          else if($this->_is_admin > 1 || $this->_is_admin < 0)
          {
            $this->error = true;
            $this->errorName = "The is_admin field is invalid!";
          }

          if(!is_numeric($this->_cc_users))
          {
            $this->error = true;
            $this->errorName = "The cc_users field is invalid!";
          }
          else if($this->_cc_users > 1 || $this->_cc_users < 0)
          {
            $this->error = true;
            $this->errorName = "The cc_users field is invalid!";
          }

          if(!$this->error)
          {
            $id = new idGenerator;
            $pw = new PasswordEncrypter;
            $rs = new RandomStringGenerator;

            $this->_uid = $id->generateId('user');
            $this->_raw_password = $rs->randomStringGenerate('8');
            $this->_password = $pw->encryptPassword($this->_raw_password);
            $this->_secret_key = hash("sha256", $rs->randomStringGenerate('16'));

            if($this->setting("enable_mailer") == 0)
            {
              $create_user = $dbh->connect()->query("INSERT INTO users (username, ign, email, password, secret_key, avatar, is_admin, cc_users, uid) VALUES ('".$this->_username."', '".$this->_ign."', '".$this->_email."', '".$this->_password."', '".$this->_secret_key."', 'default', '".$this->_is_admin."', '".$this->_cc_users."', '".$this->_uid."')");

              echo "
              <div class='card border-success text-success' style='margin-top: 15px'>
                <div class='card-body'>
                  <p style='margin: 0'><b>Email: </b><i>".$this->_email."</i></p>
                  <p style='margin: 0'><b>Password: </b><i>".$this->_raw_password."</i></p>
                </div>
              </div>
              <script type='text/javascript'>
              $(function() {
                toastr.options.preventDuplicates = true;
                toastr.options.closeButton = true;
                toastr.options.progressBar = true;

                toastr.success('User Registred!', 'Success!');

                $('#cu-form input.form-control').val('');
              });
              </script>
              ";
            }
            else
            {
              $this->_date = date('Y/m/d H:i');

              $mail = new PHPMailer(true);
              try {
                //Server settings
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->Host = $this->setting("smtp_host");
                $mail->SMTPAuth = true;
                $mail->Username = $this->setting("smtp_username");
                $mail->Password = $this->setting("smtp_password");
                $mail->CharSet = 'UTF-8';
                $mail->SMTPSecure = 'tls';
                $mail->Port = $this->setting("smtp_port");

                //Recipients
                $mail->setFrom($this->setting("smtp_username"), $this->setting("website_name"));
                $mail->addAddress($this->_email);
                $mail->addReplyTo($this->setting("smtp_username"), $this->setting("website_name"));

                //Content
                $mail->isHTML(true);
                $mail->Subject = 'Your account data on '.$this->setting("website_name");
                $mail->Body    = '
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                  <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                    <title>'.$this->setting("website_name").'</title>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
                  </head>
                  <body style="margin: 0; padding: 0;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #444b54; padding: 65px 0">
                      <tr>
                        <td>
                          <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; background-color: white; border-radius: 0.25rem; position: relative">
                            <tr>
                              <td>
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                  <tr>
                                    <td style="color: #fe6860; font-size: 34px; text-align: center; font-weight: bold; cursor: default; padding: 30px 0 20px 0">
                                      '.$this->setting("website_name").'
                                    </td>
                                  </tr>
                                  <tr>
                                    <td style="padding: 0 30px 20px 30px">
                                      <hr style="magin: 0 0 0 0; width: 100%; height: 1px; background-color: rgba(68, 75, 84, 0.90)">
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>
                                      <p style="margin: 0; color: #444b54; font-size: 18px; padding: 0 30px; font-style: italic">Dear, <b>'.$this->_username.'</b></p>
                                      <p style="magin: 0; color: #444b54; font-size: 16px; padding: 0 30px; font-style: italic">
                                        You have been registred to our website! We are glad that we can have you in our team, and if you have any issues please contact one of our administrators. And we are hopping that you will enjoy using our website!<br>
                                      </p>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>
                                      <p style="magin: 0; color: #444b54; font-size: 16px; padding: 0 30px; font-style: italic; margin: 0">
                                        <b>Email:</b> <a href="javascript:;" style="color: #fe6860; text-decoration: none">'.$this->_email.'</a>
                                      </p>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>
                                      <p style="magin: 0; color: #444b54; font-size: 16px; padding: 0 30px; font-style: italic; margin: 0">
                                        <b>Password:</b> <span style="color: #fe6860">'.$this->_raw_password.'</span>
                                      </p>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td style="padding: 30px 0 0 0">
                                      <center><a href="'.$this->setting("website_scheme").$this->setting("website_domain").'/page/login" style="color: white; background-color: #fe6860; border-radius: 0.25rem; display: table; padding: 15px 30px; font-size: 20px; font-weight: bold;text-decoration: none">Login to your account</a></center>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td style="color: #8199a3; font-style: italic; text-align: center; font-size: 12px; padding: 15px 0 45px 0">
                                      Date: '.$this->_date.'
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        <td style="text-align: center">
                          <p style="magin: 0; color: #8199a3; font-size: 12px">
                            &copy;'.date('Y').' <a href="'.$this->setting("website_scheme").$this->setting("website_domain").'" style="text-decoration: none; color: #8199a3">'.$this->setting("website_name").'</a>
                          </p>
                        </td>
                      </tr>
                    </table>
                  </body>
                </html>
                ';

                $mail->send();
                $create_user = $dbh->connect()->query("INSERT INTO users (username, ign, email, password, secret_key, avatar, is_admin, cc_users, uid) VALUES ('".$this->_username."', '".$this->_ign."', '".$this->_email."', '".$this->_password."', '".$this->_secret_key."', 'default', '".$this->_is_admin."', '".$this->_cc_users."', '".$this->_uid."')");

                echo "<script type='text/javascript'>
                $(function() {
                  toastr.options.preventDuplicates = true;
                  toastr.options.closeButton = true;
                  toastr.options.progressBar = true;

                  toastr.success('User Registred!', 'Success!');

                  $('#cu-form input.form-control').val('');
                });
                </script>";
              } catch (Exception $e) {
                echo "<script type='text/javascript'>
                $(function() {
                  toastr.options.preventDuplicates = true;
                  toastr.options.closeButton = true;
                  toastr.options.progressBar = true;

                  toastr.error('".$mail->ErrorInfo."', 'Failed!');
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
}

$cu = new CreateUser;