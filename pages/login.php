<?php
ob_start();
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("./inc/database.php");
require_once("./inc/main.php");

$main = new Main;

date_default_timezone_set($main->setting("timezone"));

class Page extends Database
{
  private $_secret_key;

  public function checkSession()
  {
    $dbh = new Database;
    $main = new Main;

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
        header("Location: ".$main->link('module/dashboard'));
      }
    }
  }
}

$page = new Page;
$page->checkSession();
?>
<html>
  <head>
    <title><?php echo $main->setting("website_name")." / Login"; ?></title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="shortcut icon" href="<?php echo $main->link($main->setting("website_favicon")); ?>">
    <!-- Browser -->
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta content="mranonymusz" name="author">
    <meta property="og:site_name" content="<?php echo $main->setting("website_name"); ?>">
    <meta property="og:title" content="<?php echo $main->setting("website_name")." / Login"; ?>">
    <meta property="og:description" content="<?php echo $main->setting("website_description"); ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?php echo $main->link($main->setting("website_image")); ?>">
    <meta property="og:url" content="<?php echo $main->link('page/login'); ?>">
    <!-- Browser -->
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/bootstrap/css/bootstrap.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/font-awesome/fontawesome-all.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/themify/themify-icons.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/pace/theme.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/magic-check/magic-check.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/toastr/toastr.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/jquery/jquery-ui.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/css/template.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/css/responsive.css"); ?>">
    <!-- Stylesheets -->
    <!-- Javascript -->
    <script src="<?php echo $main->link("assets/plugins/jquery/jquery-3.3.1.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/jquery/jquery-ui.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/bootstrap/js/popper.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/bootstrap/js/bootstrap.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/pace/pace.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/toastr/toastr.js"); ?>"></script>
    <script type="text/javascript">
    $(function() {
      $('#login-form').submit(function(e) {
        e.preventDefault();

        var email = $('[name="email"]').val();
        var password = $('[name="password"]').val();
        if($('[name="rembme"]').is(':checked'))
        {
          var checkbox = true;
        }
        else
        {
          var checkbox = false;
        }
        var submit = $('[name="submit"]').val();

        $('#login-form-output').load('<?php echo $main->link('inc/actions/login.php'); ?>', {
          method: "post",
          email: email,
          password: password,
          checkbox: checkbox,
          submit: submit
        });
      });
    });
    </script>
    <!-- Javascript -->
  </head>
  <body class="light-body">
    <!-- BODY -->
    <!-- Login -->
    <div class="login-box">
      <div class="header">
        <img src="<?php echo $main->link("assets/img/logo.png"); ?>" draggable="false" />
        <h2>Login</h2>
      </div>
      <div class="body">
        <form action="<?php echo $main->link('inc/actions/login.php'); ?>" method="post" id="login-form" autocomplete="off">
          <input type="text" name="email" class="form-control" placeholder="Email Address">
          <div class="form-icon fc-right">
            <i class="fas fa-eye" id="toggle-pw" data-toggle="tooltip" data-placement="right" title="Show Password"></i>
            <input type="password" name="password" class="form-control" placeholder="Password">
          </div>
          <hr>
          <div>
            <input class="magic-checkbox" type="checkbox" name="rembme" id="rembme">
            <label for="rembme">Remember Me</label>
          </div>
          <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block">
            <i class="fas fa-sign-in-alt"></i> Login
          </button>
        </form>
      </div>
    </div>
    <div class="login-info-box">
      <?php
      if($main->setting("enable_mailer") == 1)
      {
        echo "<p class='ca'><a href='".$main->link("page/password-reminder")."'>Password Reminder</a></p>";
      }
      ?>
      <p class="small"><?php echo "&copy;".date('Y')." ".$main->setting("website_name"); ?></p>
    </div>
    <!-- Login -->
    <!-- BODY -->
    <!-- JS -->
    <div id="login-form-output"></div>
    <script type="text/javascript">
    $(function() {
      var tpw = $('#toggle-pw');
      var pw = $('[name="password"]');

      tpw.attr('data-original-title', 'Show Password');

      tpw.click(function() {
        if(pw.attr('type') == "password") {
          pw.attr('type', 'text');

          tpw.attr('data-original-title', 'Hide Password');
        } else if(pw.attr('type') == "text") {
          pw.attr('type', 'password');

          tpw.attr('data-original-title', 'Show Password');
        }

        tpw.toggleClass('fa-eye fa-eye-slash', 1000);
      });

      $('[data-toggle="tooltip"]').tooltip({
        html: true
      });
    });
    </script>
    <!-- JS -->
  </body>
</html>
<?php
ob_end_flush();
?>