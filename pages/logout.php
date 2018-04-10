<?php
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("./inc/database.php");
require_once("./inc/main.php");

class Logout extends Database
{
  private $_setting;
  private $_secret_key;
  private $_auth_key;

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

  public function __construct()
  {
    if($this->checkSession() == true)
    {
      $this->_secret_key = $_COOKIE['secret_key'];
      $this->_auth_key = $_GET['key'];

      if($this->_secret_key == $this->_auth_key)
      {
        echo "<script type='text/javascript'>
        $(document).ready(function() {
          function setCookie(params) {
            var name            = params.name,
                value           = params.value,
                expireDays      = params.days,
                expireHours     = params.hours,
                expireMinutes   = params.minutes,
                expireSeconds   = params.seconds;
    
            var expireDate = new Date();
            if (expireDays) {
                expireDate.setDate(expireDate.getDate() + expireDays);
            }
            if (expireHours) {
                expireDate.setHours(expireDate.getHours() + expireHours);
            }
            if (expireMinutes) {
                expireDate.setMinutes(expireDate.getMinutes() + expireMinutes);
            }
            if (expireSeconds) {
                expireDate.setSeconds(expireDate.getSeconds() + expireSeconds);
            }
    
            document.cookie = name +'='+ escape(value) +
            ';domain='+ window.location.hostname +
            ';path=/'+
            ';expires='+expireDate.toUTCString();
          }

          function deleteCookie(name) {
            setCookie({name: name, value: '', seconds: 1});
          }

          deleteCookie('secret_key');

          window.location.href = '".$this->setting("website_scheme").$this->setting("website_domain")."/page/login';
        });
        </script>";
      }
      else
      {
        echo "<script type='text/javascript'>
        $(document).ready(function() {
          window.location.href = '".$this->setting("website_scheme").$this->setting("website_domain")."/module/dashboard';
        });
        </script>";
      }
    }
    else
    {
      echo "<script type='text/javascript'>
      $(document).ready(function() {
        window.location.href = '".$this->setting("website_scheme").$this->setting("website_domain")."/page/login';
      });
      </script>";
    }
  }
}

$main = new Main;
?>
<html>
  <head>
    <script src="<?php echo $main->link("assets/plugins/jquery/jquery-3.3.1.js"); ?>"></script>
  </head>
  <body>
    <?php $logout = new Logout; ?>
  </body>
</html>