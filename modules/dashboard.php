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

  public function countReports()
  {
    $dbh = new Database;
    $main = new Main;

    $this->_table = $main->setting("table_name");

    $select_reports = $dbh->connect()->query("SELECT ID FROM $this->_table");
    $count_reports = $select_reports->num_rows;

    if(strlen($count_reports) > 8)
    {
      return substr($count_reports, 0, 2)."M+";
    }
    else 
    {
      return number_format($count_reports, 0, '.', ' ');
    }
  }

  public function countUnReports()
  {
    $dbh = new Database;
    $main = new Main;

    $this->_table = $main->setting("table_name");

    $select_reports = $dbh->connect()->query("SELECT ID FROM $this->_table WHERE ticketManager='none ' AND resolving='0'");
    $count_reports = $select_reports->num_rows;

    if(strlen($count_reports) > 8)
    {
      return substr($count_reports, 0, 2)."M+";
    }
    else 
    {
      return number_format($count_reports, 0, '.', ' ');
    }
  }

  public function countReReports()
  {
    $dbh = new Database;
    $main = new Main;

    $this->_table = $main->setting("table_name");

    $select_reports = $dbh->connect()->query("SELECT ID FROM $this->_table WHERE open='0' AND resolving='0'");
    $count_reports = $select_reports->num_rows;

    if(strlen($count_reports) > 8)
    {
      return substr($count_reports, 0, 2)."M+";
    }
    else 
    {
      return number_format($count_reports, 0, '.', ' ');
    }
  }

  public function countMyReports()
  {
    $dbh = new Database;

    $this->_uname = $this->user("username");

    $select_reports = $dbh->connect()->query("SELECT * FROM users WHERE username='$this->_uname'");
    $display_reports = $select_reports->fetch_array();

    $exploder_rr = explode(',', $display_reports['resolved_reports']);

    if(empty($exploder_rr[0]))
    {
      return 0;
    }
    else
    {
      return count($exploder_rr);
    }
  }
}

$module = new Module;
$module->checkSession();
?>
<html>
  <head>
    <title><?php echo $main->setting("website_name")." / Dashboard"; ?></title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="shortcut icon" href="<?php echo $main->link($main->setting("website_favicon")); ?>">
    <!-- Browser -->
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta content="mranonymusz" name="author">
    <meta property="og:site_name" content="<?php echo $main->setting("website_name"); ?>">
    <meta property="og:title" content="<?php echo $main->setting("website_name")." / Dashboard"; ?>">
    <meta property="og:description" content="<?php echo $main->setting("website_description"); ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?php echo $main->link($main->setting("website_image")); ?>">
    <meta property="og:url" content="<?php echo $main->link('module/dashboard'); ?>">
    <!-- Browser -->
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/bootstrap/css/bootstrap.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/font-awesome/fontawesome-all.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/themify/themify-icons.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/toastr/toastr.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/jquery/jquery-ui.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/metismenu/metisMenu.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.css"); ?>">
    <link rel="stylesheet" href="<?php echo $main->link("assets/plugins/datatables/Buttons-1.5.1/css/buttons.bootstrap4.css"); ?>">
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
    <script src="<?php echo $main->link("assets/plugins/datatables/JSZip-2.5.0/jszip.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/datatables/pdfmake-0.1.32/pdfmake.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/datatables/pdfmake-0.1.32/vfs_fonts.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/datatables/Buttons-1.5.1/js/dataTables.buttons.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/datatables/Buttons-1.5.1/js/buttons.bootstrap4.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/datatables/Buttons-1.5.1/js/buttons.colVis.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/datatables/Buttons-1.5.1/js/buttons.flash.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/datatables/Buttons-1.5.1/js/buttons.html5.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/datatables/Buttons-1.5.1/js/buttons.print.js"); ?>"></script>
    <script src="<?php echo $main->link("assets/plugins/datatables/Buttons-1.5.1/js/dataTables.buttons.js"); ?>"></script>
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
        <li class="breadcrumb-item active" aria-current="page">Home</li>
      </ol>
      <!-- Breadcrumb -->
      <div class="container">
        <div class="row">
          <!-- Widgets -->
          <div class="col-md-3">
            <div class="widget bg-primary" style="margin-bottom: 15px">
              <div class="left">
                <i class="fas fa-list"></i>
              </div>
              <div class="right">
                <h4 class="title">Reports</h4>
                <p class="value"><?php echo $module->countReports(); ?></p>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="widget bg-danger" style="margin-bottom: 15px">
              <div class="left">
                <i class="fas fa-times-circle"></i>
              </div>
              <div class="right">
                <h4 class="title">Un. Reports</h4>
                <p class="value"><?php echo $module->countUnReports(); ?></p>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="widget bg-success" style="margin-bottom: 15px">
              <div class="left">
                <i class="fas fa-check-circle"></i>
              </div>
              <div class="right">
                <h4 class="title">Re. Reports</h4>
                <p class="value"><?php echo $module->countReReports(); ?></p>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="widget bg-info" style="margin-bottom: 15px">
              <div class="left">
                <i class="fas fa-chart-bar"></i>
              </div>
              <div class="right">
                <h4 class="title">My Reports</h4>
                <p class="value">
                  <?php echo $module->countMyReports(); ?>
                </p>
              </div>
            </div>
          </div>
          <!-- Widgets -->
          <!-- Alert -->
          <div class="col-md-12">
            <div class="card text-center">
              <div class="card-header">
                <i class="fas fa-exclamation-circle"></i>
              </div>
              <div class="card-body">
                <h5 class="card-title">WebGui v2</h5>
                <p class="card-text">Thanks for using the WebGui for the <a href="https://www.spigotmc.org/resources/advancedreporter-report-✦-web-gui-✦-mysql-in-game-gui.22580/" target="_blank">AdvancedReporter</a> minecraft plugin :) You can find MrAnonymusz and Nexgan on the following discord server. If you find any bugs or you have any suggestions go to the gui's github page.</p>
                <a href="https://discord.gg/4SxSuNM" class="btn btn-success btn-lg" target="_blank"><i class="fab fa-discord"></i> Discord</a>
              </div>
              <div class="card-footer text-muted">
                <?php echo date('Y/m/d H:i'); ?>
              </div>
            </div>
          </div>
          <!-- Alert -->
          <!-- Reports -->
          <div class="col-md-12">
            <div class="portlet" style="margin-top: 15px">
              <div class="portlet-header">
                <h4 class="portlet-title"><i class="fas fa-times-circle"></i> Unresolved Reports</h4>
              </div>
              <div class="portlet-body">
                <div class="table-responsive">
                  <table class="table table-striped table-hover" id="un-reports">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Reported</th>
                        <th>Reporter</th>
                        <th>Status</th>
                        <th>Section</th>
                        <th>ServerName</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="portlet" style="margin-top: 15px">
              <div class="portlet-header">
                <h4 class="portlet-title"><i class="fas fa-times-circle"></i> Resolved Reports</h4>
              </div>
              <div class="portlet-body">
                <div class="table-responsive">
                  <table class="table table-striped table-hover" id="re-reports">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Reported</th>
                        <th>Reporter</th>
                        <th>Status</th>
                        <th>Section</th>
                        <th>ServerName</th>
                        <th>TicketManager</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- Reports -->
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

      $('#un-reports').DataTable({
        ajax: {
          url: '<?php echo $main->link("inc/actions/lur.php"); ?>',
          dataSrc: ''
        },
        order: [[0, 'desc']],
        dom: 'Bfrtip',
        buttons: [
          'copy',
          'print',
          'csv',
          'excel',
          'pdf'
        ],
        columns: [
          {data: "id"},
          {data: "reported"},
          {data: "reporter"},
          {data: "status"},
          {data: "section"},
          {data: "serverName"},
          {data: "action"}
        ]
      });

      $('#re-reports').DataTable({
        ajax: {
          url: '<?php echo $main->link("inc/actions/lrr.php"); ?>',
          dataSrc: ''
        },
        order: [[0, 'desc']],
        dom: 'Bfrtip',
        buttons: [
          'copy',
          'print',
          'csv',
          'excel',
          'pdf'
        ],
        columns: [
          {data: "id"},
          {data: "reported"},
          {data: "reporter"},
          {data: "status"},
          {data: "section"},
          {data: "serverName"},
          {data: "ticketManager"}
        ]
      });
    });
    </script>
    <!-- JS -->
  </body>
</html>
<?php
ob_end_flush();
?>