<?php
ob_start();
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("./inc/database.php");
require_once("./inc/main.php");

$main = new Main;

date_default_timezone_set($main->setting("timezone"));
?>
<html>
  <head>
    <title><?php echo $main->setting("website_name")." / Submit Report"; ?></title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="shortcut icon" href="<?php echo $main->link($main->setting("website_favicon")); ?>">
    <!-- Browser -->
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta content="mranonymusz" name="author">
    <meta property="og:site_name" content="<?php echo $main->setting("website_name"); ?>">
    <meta property="og:title" content="<?php echo $main->setting("website_name")." / Submit Report"; ?>">
    <meta property="og:description" content="<?php echo $main->setting("website_description"); ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?php echo $main->link($main->setting("website_image")); ?>">
    <meta property="og:url" content="<?php echo $main->link('page/submit-report'); ?>">
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
      $('#sr-form').submit(function(e) {
        e.preventDefault();

        var reported = $('[name="reported"]').val();
        var reporter = $('[name="reporter"]').val();
        var reason = $('[name="reason"]').val();
        var submit = $('[name="submit"]').val();

        $('#sr-output').load('<?php echo $main->link("inc/actions/create_report.php"); ?>', {
          method: "post",
          reported: reported,
          reporter: reporter,
          reason: reason,
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
        <h2>Submit Report</h2>
      </div>
      <div class="body">
        <form action="<?php echo $main->link("inc/actions/create_report.php"); ?>" method="post" id="sr-form" autocomplete="off">
          <input type="text" name="reported" class="form-control" placeholder="Reported Person">
          <input type="text" name="reporter" class="form-control" placeholder="Your Name">
          <textarea name="reason" class="form-control" placeholder="Reason for the report" maxlength="200"></textarea>
          <p class="wc"><span id="cw">0</span>/200</p>
          <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block">
            <i class="fas fa-paper-plane"></i> Submit Report
          </button>
        </form>
      </div>
    </div>
    <div class="login-info-box">
      <p class="ca">
        <a href="<?php echo $main->link("page/login"); ?>">
          <i class="fas fa-chevron-circle-right"></i> Back to Login
        </a>
      </p>
      <p class="small"><?php echo "&copy;".date('Y')." ".$main->setting("website_name"); ?></p>
    </div>
    <!-- Login -->
    <!-- BODY -->
    <!-- JS -->
    <div id="sr-output"></div>
    <script type="text/javascript">
    $(function() {
      var str_lenght;

      $('[name="reason"]').keyup(function() {
        str_lenght = jQuery('[name="reason"]').val().length;

        if(str_lenght >= 200)
        {
          if(!$('.wc').hasClass("text-danger"))
          {
            $('.wc').addClass("text-danger");
          }
        }
        else if(str_lenght < 200)
        {
          if($('.wc').hasClass("text-danger"))
          {
            $('.wc').removeClass("text-danger");
          }
        }

        $('#cw').text(str_lenght);
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