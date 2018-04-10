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
  private $_auth_key;

  public function checkSession()
  {
    $this->_secret_key = $_COOKIE['secret_key'];

    $dbh = new Database;
    $main = new Main;

    if(!empty($this->_secret_key))
    {
      $this->_secret_key = mysqli_real_escape_string($dbh->connect(), $_COOKIE['secret_key']);
      $this->_secret_key = htmlspecialchars($this->_secret_key);
      $this->_secret_key = trim($this->_secret_key);
      
      $select_key = $dbh->connect()->query("SELECT uid FROM users WHERE secret_key='$this->_secret_key'");
      $check_key = $select_key->num_rows;

      if($check_key == 1)
      {
        header("Location: ".$main->link("module/dashboard"));
      }
    }
  }

  public function checkKey()
  {
    $this->_auth_key = $_GET['key'];

    $dbh = new Database;
    $main = new Main;

    if(!empty($this->_auth_key))
    {
      $select_key = $dbh->connect()->query("SELECT email FROM pw_reminder WHERE pwr_key='pwr_$this->_auth_key'");
      $check_key = $select_key->num_rows;

      if($check_key != 1)
      {
        header("Location: ".$main->link("page/login"));
      }
    }
  }

  public function checkMailer()
  {
    $main = new Main;

    if($main->setting("enable_mailer") != 1)
    {
      header("Location: ".$main->link("page/login"));
    }
  }
}

$page = new Page;
$page->checkMailer();
$page->checkSession();
$page->checkKey();
?>
<html>
  <head>
    <title><?php echo $main->setting("website_name")." / Change Password"; ?></title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="shortcut icon" href="<?php echo $main->link($main->setting("website_favicon")); ?>">
    <!-- Browser -->
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta content="mranonymusz" name="author">
    <meta property="og:site_name" content="<?php echo $main->setting("website_name"); ?>">
    <meta property="og:title" content="<?php echo $main->setting("website_name")." / Change Password"; ?>">
    <meta property="og:description" content="<?php echo $main->setting("website_description"); ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?php echo $main->link($main->setting("website_image")); ?>">
    <meta property="og:url" content="<?php echo $main->link('page/change-password/').$_GET['key']; ?>">
    <!-- Browser -->
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/bootstrap/css/bootstrap.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/font-awesome/fontawesome-all.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/themify/themify-icons.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/pace/theme.css"); ?>">
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
      $('#change-pw-form').submit(function(e) {
        e.preventDefault();

        var key = "<?php echo $_GET['key']; ?>";
        var submit = $('[name="submit"]').val();

        $('#change-pw-form-output').load('<?php echo $main->link('inc/actions/change_password.php'); ?>', {
          method: "post",
          key: key,
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
        <h2>Change Password</h2>
      </div>
      <div class="body">
        <p class="cpw_key">Key: <?php echo $_GET['key']; ?></p>
        <form action="<?php echo $main->link('inc/actions/change_password.php'); ?>" method="post" id="change-pw-form" autocomplete="off">
          <input type="text" name="password" class="form-control" placeholder="New Password" disabled="disabled">
          <hr>
          <button type="submit" name="submit" class="btn btn-success btn-lg btn-block">
            <i class="fas fa-check-circle"></i> Get Password
          </button>
        </form>
      </div>
    </div>
    <div class="login-info-box">
      <p class="ca"><a href="<?php echo $main->link("page/login"); ?>"><i class="fas fa-chevron-circle-right"></i> Back to Login</a></p>
      <p class="small"><?php echo "&copy;".date('Y')." ".$main->setting("website_name"); ?></p>
    </div>
    <!-- Login -->
    <!-- BODY -->
    <!-- JS -->
    <div id="change-pw-form-output"></div>
    <!-- JS -->
  </body>
</html>
<?php
ob_end_flush();
?>