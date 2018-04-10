<?php
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("../database.php");
require_once("../id_generator.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once("../phpmailer/Exception.php");
require_once("../phpmailer/PHPMailer.php");
require_once("../phpmailer/SMTP.php");

class Reminder extends Database
{
  private $_email;
  private $_secret_key;
  private $_setting;
  private $_ip;
  private $_date;
  private $_key;

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
    $dbh = new Database;
    $id = new idGenerator;

    $this->_secret_key = $_COOKIE['secret_key'];

    if(empty($this->_secret_key))
    {
      if($this->setting("enable_mailer") == 1)
      {
        $this->error = false;

        if(isset($_POST['submit']))
        {
          $this->_email = mysqli_real_escape_string($dbh->connect(), $_POST['email']);
          $this->_email = htmlspecialchars($this->_email);
          $this->_email = trim($this->_email);

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

          if(!$this->error)
          {
            $select_email = $dbh->connect()->query("SELECT uid FROM users WHERE email='$this->_email'");
            $check_email = $select_email->num_rows;

            if($check_email == 1)
            {
              $select_pwr = $dbh->connect()->query("SELECT pwr_key FROM pw_reminder WHERE email='$this->_email'");
              $check_pwr = $select_pwr->num_rows;

              if($check_pwr == 0)
              {
                date_default_timezone_set($this->setting("timezone"));

                $this->_date = date('Y/m/d H:i');
                $this->_key = $id->generateId("pwr");

                $ex_key = explode('_', $this->_key);

                $json = json_decode(file_get_contents("http://ip-api.com/json", false), true);
                $this->_ip = $json['query'];

                $select_user = $dbh->connect()->query("SELECT username, ign FROM users WHERE email='$this->_email'");
                $display_user = $select_user->fetch_array();

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
                  $mail->Subject = 'Password Reminder From '.$this->setting("website_name");
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
                                        <p style="margin: 0; color: #444b54; font-size: 18px; padding: 0 30px; font-style: italic">Dear, <b>'.$display_user['username'].'('.$display_user['ign'].')</b></p>
                                        <p style="magin: 0; color: #444b54; font-size: 16px; padding: 0 30px; font-style: italic">Someone with the following ip address <b>'.$this->_ip.'</b> from <b>'.$json['country'].'</b> requested a password recovery. If it wasn'."'".'t you please click <a href="'.$this->setting("website_scheme").$this->setting("website_domain").'/module/account" style="color: #fe6860; text-decoration: none">here</a>! But if it was you and you want to change your password click on the button and you will get a new password.</p>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td style="padding: 30px 0 0 0">
                                        <center><a href="'.$this->setting("website_scheme").$this->setting("website_domain").'/page/change-password/'.$ex_key[1].'" style="color: white; background-color: #fe6860; border-radius: 0.25rem; display: table; padding: 15px 30px; font-size: 20px; font-weight: bold;text-decoration: none">Change Password</a></center>
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
                  $add_pwr = $dbh->connect()->query("INSERT INTO pw_reminder (email, date, ip, pwr_key) VALUES ('".$this->_email."', '".$this->_date."', '".$this->_ip."', '".$this->_key."')");

                  echo "<script type='text/javascript'>
                  $(function() {
                    toastr.options.preventDuplicates = true;
                    toastr.options.closeButton = true;
                    toastr.options.progressBar = true;

                    toastr.success('Mail sent!', 'Success!');

                    $('#pw-reminder-form input.form-control').val('');
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
              else
              {
                echo "<script type='text/javascript'>
                $(function() {
                  toastr.options.preventDuplicates = true;
                  toastr.options.closeButton = true;
                  toastr.options.progressBar = true;

                  toastr.error('There is already a new password request for this account!', 'Failed!');

                  $('#pw-reminder-form input.form-control').val('');
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

                toastr.error('User with this email address could not be found!', 'Failed!');

                $('#pw-reminder-form input.form-control').val('');
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
          window.location.href = '".$this->setting("website_scheme").$this->setting("website_domain")."/page/login';
        });
        </script>";
      }
    }
    else
    {
      $this->_secret_key = mysqli_real_escape_string($dbh->connect(), $_COOKIE['secret_key']);
      $this->_secret_key = htmlspecialchars($this->_secret_key);
      $this->_secret_key = trim($this->_secret_key);
      
      $select_key = $dbh->connect()->query("SELECT uid FROM users WHERE secret_key='$this->_secret_key'");
      $check_key = $select_key->fetch_array();

      if($check_key != 0 && $check_key <= 1)
      {
        echo "<script type='text/javascript'>
        $(function() {
          window.location.href = '".$this->setting("website_scheme").$this->setting("website_domain")."/module/dashboard';
        });
        </script>";
      }
    }
  }
}

$rem = new Reminder;