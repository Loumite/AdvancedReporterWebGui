<?php
ob_start();
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("./inc/database.php");
require_once("./inc/main.php");

$main = new Main;

date_default_timezone_set($main->setting("timezone"));

class Module extends Database
{
  private $_secret_key;
  private $_user;
  private $_table;
  private $_uname;

  public function checkSession()
  {
    $dbh = new Database;
    $main = new Main;

    $this->_secret_key = $_COOKIE['secret_key'];

    if(empty($this->_secret_key))
    {
      header("Location: ".$main->link("page/login"));
    }
    else
    {
      $this->_secret_key = mysqli_real_escape_string($dbh->connect(), $_COOKIE['secret_key']);
      $this->_secret_key = htmlspecialchars($this->_secret_key);
      $this->_secret_key = trim($this->_secret_key);
      
      $select_key = $dbh->connect()->query("SELECT uid FROM users WHERE secret_key='$this->_secret_key'");
      $check_key = $select_key->num_rows;

      if($check_key != 1)
      {
        header("Location: ".$main->link("page/login"));
      }
    }
  }

  public function user($_user)
  {
    $dbh = new Database;

    $this->_user = $_user;
    $this->_secret_key = $_COOKIE['secret_key'];

    $select_user = $dbh->connect()->query("SELECT * FROM users WHERE secret_key='$this->_secret_key'");
    $display_user = $select_user->fetch_array();

    return $display_user[$this->_user];
  }

  // Page Options

  public function loadSettings()
  {
    $dbh = new Database;

    $select_settings = $dbh->connect()->query("SELECT * FROM settings");

    echo "<div class='row'>";

    while($display_settings = $select_settings->fetch_array())
    {
      echo "<div class='col-md-6'>";
      echo "<p class='form-title'>".$display_settings['name']." <i>(".$display_settings['type'].")</i></p>";

      if($display_settings['type'] == "string")
      {
        echo "<textarea class='form-control' name='".$display_settings['name']."'>".$display_settings['value']."</textarea>";
      }
      else if($display_settings['type'] == "int")
      {
        echo "<input type='text' class='form-control' name='".$display_settings['name']."' value='".$display_settings['value']."'>";
      }
      else if($display_settings['type'] == "boolean")
      {
        echo "<select class='form-control' name='".$display_settings['name']."'>";
          if($display_settings['value'] == 1)
          {
            echo "<option value='1' selected>True</option>";
            echo "<option value='0'>False</option>";
          }
          else if($display_settings['value'] != 1)
          {
            echo "<option value='1'>True</option>";
            echo "<option value='0' selected>False</option>";
          }
        echo "</select>";
      }

      echo "</div>";
    }

    echo "</div>";
  }

  public function loadJSSetting($ljss = 1)
  {
    $dbh = new Database;

    $this->ljss = $ljss;

    $select_settings = $dbh->connect()->query("SELECT * FROM settings");

    if($this->ljss == 1)
    {
      while($display_settings = $select_settings->fetch_array())
      {
        echo "var ".$display_settings['name']." = $('[name=".'"'.$display_settings['name'].'"'."]').val();";
      }
    }
    else if($this->ljss == 2)
    {
      while($display_settings = $select_settings->fetch_array())
      {
        echo $display_settings['name'].": ".$display_settings['name'].",";
      }
    }
  }
}

$module = new Module;
$module->checkSession();
?>
<html>
  <head>
    <title><?php echo $main->setting("website_name")." / Settings"; ?></title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="shortcut icon" href="<?php echo $main->link($main->setting("website_favicon")); ?>">
    <!-- Browser -->
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta content="mranonymusz" name="author">
    <meta attrerty="og:site_name" content="<?php echo $main->setting("website_name"); ?>">
    <meta attrerty="og:title" content="<?php echo $main->setting("website_name")." / Settings"; ?>">
    <meta attrerty="og:description" content="<?php echo $main->setting("website_description"); ?>">
    <meta attrerty="og:type" content="website">
    <meta attrerty="og:image" content="<?php echo $main->link($main->setting("website_image")); ?>">
    <meta attrerty="og:url" content="<?php echo $main->link('module/settings'); ?>">
    <!-- Browser -->
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/bootstrap/css/bootstrap.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/font-awesome/fontawesome-all.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/themify/themify-icons.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/toastr/toastr.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/jquery/jquery-ui.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/metismenu/metisMenu.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/datatables/Responsive-2.2.1/css/responsive.bootstrap4.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/css/template.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/css/navigation.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/css/responsive.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/css/animate.css"); ?>">
    <!-- Stylesheets -->
    <!-- Javascript -->
    <script src="<?php echo $main->link("assets/plugins/jquery/jquery-3.3.1.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/jquery/jquery-ui.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/bootstrap/js/popper.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/bootstrap/js/bootstrap.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/metismenu/metisMenu.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/toastr/toastr.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/js/navigation.js"); ?>"></script>
    <!-- Javascript -->
  </head>
  <body>
    <!-- BODY -->
    <!-- Navigation -->
    <?php require_once("./inc/components/navigation.php"); ?>
    <!-- Navigation -->
    <!-- Content -->
    <div class="content">
      <!-- Breadcrumb -->
      <ol class="content-breadcrumb breadcrumb">
        <li class="breadcrumb-item">
          <a href="<?php echo $main->link('module/dashboard'); ?>">Home</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Settings</li>
      </ol>
      <!-- Breadcrumb -->
      <div class="container">
        <div class="row">
          <!-- Account Settings -->
          <div class="col-md-6">
            <div class="portlet">
              <div class="portlet-header">
                <h4 class="portlet-title"><i class="fas fa-cog"></i> Account Settings</h4>
              </div>
              <div class="portlet-body">
                <form action="<?php echo $main->link('inc/actions/update_user.php'); ?>" method="post" autocomplete="off" id="cas-form">
                  <input type="text" name="username" class="form-control" placeholder="Username" value="<?php echo $module->user("username"); ?>">
                  <input type="text" name="ign" class="form-control" placeholder="IGN" value="<?php echo $module->user("ign"); ?>">
                  <input type="text" name="email" class="form-control" placeholder="Email" value="<?php echo $module->user("email"); ?>">
                  <div class="form-icon fc-right" style="margin-bottom: 10px">
                    <i class="fas fa-question-circle" id="avatar-info"></i>
                    <input type="text" name="avatar" class="form-control" placeholder="Avatar" value="<?php echo $module->user("avatar"); ?>">
                  </div>
                  <div class="form-icon fc-right">
                    <i class="fas fa-eye" id="toggle-pw" data-toggle="tooltip" data-placement="right" title="Show Password"></i>
                    <input type="password" name="password" class="form-control" placeholder="Password">
                  </div>
                  <hr>
                  <button type="submit" name="submit" class="btn btn-success btn-lg btn-block">
                    <i class="fas fa-save"></i> Save
                  </button>
                </form>
                <div id="cas-output"></div>
              </div>
            </div>
          </div>
          <!-- Account Settings -->
          <!-- My Reports -->
          <div class="col-md-6">
            <div class="portlet">
              <div class="portlet-header">
                <h4 class="portlet-title"><i class="fas fa-archive"></i> My Reports</h4>
              </div>
              <div class="portlet-body">
                <div class="table-responsive">
                  <table class="table table-striped" id="my-reports">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Reported</th>
                        <th>Reporter</th>
                        <th>Status</th>
                        <th>Server</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- My Reports -->
          <?php
          if($module->user("is_admin") == 1)
          {
          ?>
          <!-- Settings -->
          <script type="text/javascript">
          $(function() {
            $('#set-form').submit(function(e) {
              e.preventDefault();

              <?php echo $module->loadJSSetting(); ?>
              var submit = $('#set-form [name="set_submit"]').val();

              $('#set-output').load('<?php echo $main->link("inc/actions/update_settings.php") ?>', {
                method: "post",
                <?php echo $module->loadJSSetting(2); ?>
                submit: submit
              });
            });
          });
          </script>
          <div class="col-md-12">
            <div class="portlet" style="margin-top: 15px">
              <div class="portlet-header">
                <h4 class="portlet-title"><i class="fas fa-cogs"></i> Settings</h4>
              </div>
              <div class="portlet-body">
                <form action="<?php echo $main->link("inc/actions/update_settings.php") ?>" method="post" autocomplete="off" id="set-form">
                  <?php
                  $module->loadSettings();
                  ?>
                  <button type="submit" name="set_submit" class="btn btn-success btn-lg btn-block">
                    <i class="fas fa-save"></i> Save
                  </button>
                </form>
              </div>
            </div>
          </div>
          <div id="set-output"></div>
          <!-- Settings -->
          <?php
          }
          ?>
        </div>
      </div>
      <!-- Footer -->
      <?php require_once("./inc/components/footer.php"); ?>
      <!-- Footer -->
    </div>
    <!-- Content -->
    <!-- BODY -->
    <!-- JS -->
    <script type="text/javascript">
    $(function() {
      $('[data-toggle="tooltip"]').tooltip();

      $('#cas-form').submit(function(e) {
        e.preventDefault();

        var username = $('[name="username"]').val();
        var ign = $('[name="ign"]').val();
        var email = $('[name="email"]').val();
        var avatar = $('[name="avatar"]').val();
        var password = $('[name="password"]').val();
        var submit = $('[name="submit"]').val();

        $('#cas-output').load('<?php echo $main->link("inc/actions/update_user.php"); ?>', {
          method: "post",
          username: username,
          ign: ign,
          email: email,
          avatar: avatar,
          password: password,
          submit: submit
        });
      });

      $('#avatar-info').popover({
        html: true,
        placement: 'right',
        trigger: 'hover',
        content: 'You can use two default presets for the avatar <code>default</code> to use the default avatar, <code>mc</code> to use the <code>IGN</code>s minecraft avatar or just simply enter a link to use a custom avatar.'
      });

      var pw = $('#cas-form input[name="password"]');
      var tpw = $('#toggle-pw');

      tpw.attr('data-original-title', 'Show Password');

      tpw.click(function() {
        if(pw.attr('type') == "password")
        {
          pw.attr('type', 'text');

          tpw.attr('data-original-title', 'Hide Password');
        }
        else if(pw.attr('type') == "text")
        {
          pw.attr('type', 'password');

          tpw.attr('data-original-title', 'Show Password');
        }

        $('#toggle-pw').toggleClass('fa-eye fa-eye-slash');
      });

      $('#my-reports').DataTable({
        ajax: {
          url: '<?php echo $main->link("inc/actions/lmr.php"); ?>',
          dataSrc: ''
        },
        columns: [
          { data: 'id' },
          { data: 'reported' },
          { data: 'reporter' },
          { data: 'status' },
          { data: 'server' },
          { data: 'action' }
        ],
        order: [[0, 'desc']]
      });
    });
    </script>
    <!-- JS -->
  </body>
</html>
<?php
ob_end_flush();
?>