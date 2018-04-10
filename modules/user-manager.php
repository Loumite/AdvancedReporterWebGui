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

  public function checkUser()
  {
    $main = new Main;

    if($this->user("is_admin") != 1)
    {
      header("Location: ".$main->link("module/dashboard"));
    }
  }
}

$module = new Module;
$module->checkSession();
$module->checkUser();
?>
<html>
  <head>
    <title><?php echo $main->setting("website_name")." / User Manager"; ?></title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="shortcut icon" href="<?php echo $main->link($main->setting("website_favicon")); ?>">
    <!-- Browser -->
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta content="mranonymusz" name="author">
    <meta property="og:site_name" content="<?php echo $main->setting("website_name"); ?>">
    <meta property="og:title" content="<?php echo $main->setting("website_name")." / User Manager"; ?>">
    <meta property="og:description" content="<?php echo $main->setting("website_description"); ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?php echo $main->link($main->setting("website_image")); ?>">
    <meta property="og:url" content="<?php echo $main->link('module/user-manager'); ?>">
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
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/magic-check/magic-check.css"); ?>">
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
        <li class="breadcrumb-item active" aria-current="page">User Manager</li>
      </ol>
      <!-- Breadcrumb -->
      <div class="container">
        <div class="row">
          <!-- Create User -->
          <div class="col-md-6">
            <div class="portlet">
              <div class="portlet-header">
                <h4 class="portlet-title"><i class="fas fa-user-plus"></i> Create User</h4>
              </div>
              <div class="portlet-body">
                <?php
                if($module->user("cc_users") == 1)
                {
                ?>
                <script type="text/javascript">
                $(function() {
                  $('#cu-form').submit(function(e) {
                    e.preventDefault();

                    var username = $('[name="username"]').val();
                    var ign = $('[name="ign"]').val();
                    var email = $('[name="email"]').val();
                    var submit = $('[name="submit"]').val();
                    var is_admin;
                    var cc_users;

                    if($('[name="is_admin"]').is(':checked')) {
                      is_admin = 1;
                    }
                    else
                    {
                      is_admin = 0;
                    }
                    if($('[name="cc_users"]').is(':checked')) {
                      cc_users = 1;
                    }
                    else
                    {
                      cc_users = 0;
                    }

                    $('#cu-output').load('<?php echo $main->link("inc/actions/create_user.php"); ?>', {
                      method: "post",
                      username: username,
                      ign: ign,
                      email: email,
                      submit: submit,
                      is_admin: is_admin,
                      cc_users: cc_users
                    });
                  });

                  $('#ru-form').submit(function(e) {
                    e.preventDefault();

                    var ru_email = $('[name="ru_email"]').val();
                    var ru_submit = $('[name="ru_submit"]').val();

                    $('#cu-output').load('<?php echo $main->link("inc/actions/remove_user.php"); ?>', {
                      method: "post",
                      email: ru_email,
                      submit: ru_submit
                    });
                  });
                });
                </script>
                <form action="<?php echo $main->link("inc/actions/create_user.php"); ?>" method="post" autocomplete="off" id="cu-form">
                  <div class="form-icon fc-right">
                    <i class="fas fa-asterisk text-danger" data-toggle="tooltip" data-placement="right" title="Required Field!"></i>
                    <input type="text" name="username" class="form-control" placeholder="Enter a username!">
                  </div>
                  <div class="form-icon fc-right">
                    <i class="fas fa-asterisk text-danger" data-toggle="tooltip" data-placement="right" title="Required Field!"></i>
                    <input type="text" name="ign" class="form-control" placeholder="InGameName!">
                  </div>
                  <div class="form-icon fc-right">
                    <i class="fas fa-asterisk text-danger" data-toggle="tooltip" data-placement="right" title="Required Field!"></i>
                    <input type="text" name="email" class="form-control" placeholder="Enter an email address!">
                  </div>
                  <div>
                    <input class="magic-checkbox" type="checkbox" name="is_admin" id="1">
                    <label for="1">Is admin ?</label>
                  </div>
                  <div>
                    <input class="magic-checkbox" type="checkbox" name="cc_users" id="2">
                    <label for="2">Can create users ?</label>
                  </div>
                  <hr>
                  <button type="submit" name="submit" class="btn btn-success btn-lg btn-block">
                    <i class="fas fa-check-circle"></i> Create User
                  </button>
                </form>
                <h4 style="margin: 15px 0"><i class="fas fa-minus-circle"></i> Remove User</h4>
                <hr>
                <form action="<?php echo $main->link('inc/actions/remove_user.php'); ?>" method="post" autocomplete="off" id="ru-form">
                  <input type="text" name="ru_email" class="form-control" placeholder="Email Address!">
                  <button type="submit" name="ru_submit" class="btn btn-danger btn-lg btn-block">
                    <i class="fas fa-trash-alt"></i> Remove User
                  </button>
                </form>
                <div id="cu-output"></div>
                <?php
                }
                else
                {
                ?>
                <div class="alert alert-danger" role="alert">
                  <h4 class="alert-heading">Oops! <i class="far fa-frown"></i></h4>
                  <p>Sorry but you can't create or delete users, if you have any problems about that please contact an administrator or try again later!</p>
                </div>
                <?php
                }
                ?>
              </div>
            </div>
          </div>
          <!-- Create User -->
          <!-- User List -->
          <div class="col-md-6">
            <div class="portlet">
              <div class="portlet-header">
                <h4 class="portlet-title"><i class="fas fa-users"></i> Users</h4>
              </div>
              <div class="portlet-body">
                <div class="table-responsive">
                  <table class="table table-striped" id="users">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Avatar</th>
                        <th>Username</th>
                        <th>IGN</th>
                        <th>Email</th>
                        <th>Rank</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- User List -->
          <div style="color: transparent; background-color: transparent; width: 100%; height: 300px"></div>
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

      $('#users').DataTable({

        ajax: {
          url: '<?php echo $main->link('inc/actions/load_users.php'); ?>',
          dataSrc: ''
        },
        columns: [
          { data: 'id' },
          { data: 'avatar' },
          { data: 'username' },
          { data: 'ign' },
          { data: 'email' },
          { data: 'rank' }
        ],
        order: [[0, "desc"]]
      });
    });
    </script>
    <!-- JS -->
  </body>
</html>
<?php
ob_end_flush();
?>