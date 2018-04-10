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
  private $_id;
  private $_report;

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
  public function checkKey()
  {
    $dbh = new Database;
    $main = new Main;

    $this->_table = $main->setting("table_name");

    $this->_id = mysqli_real_escape_string($dbh->connect(), $_GET['id']);
    $this->_id = htmlspecialchars($this->_id);
    $this->_id = trim($this->_id);

    $select_report = $dbh->connect()->query("SELECT reported FROM $this->_table WHERE ID='$this->_id'");
    $check_report = $select_report->num_rows;

    if($check_report != 1)
    {
      header("Location: ".$main->link("module/dashboard"));
    }
  }

  public function report($_report)
  {
    $dbh = new Database;
    $main = new Main;

    $this->_report = $_report;
    $this->_table = $main->setting("table_name");

    $this->_id = mysqli_real_escape_string($dbh->connect(), $_GET['id']);
    $this->_id = htmlspecialchars($this->_id);
    $this->_id = trim($this->_id);

    $select_report = $dbh->connect()->query("SELECT * FROM $this->_table WHERE ID='$this->_id'");
    $display_report = $select_report->fetch_array();

    return $display_report[$this->_report];
  }

  public function checkUser()
  {
    $main = new Main;

    if($this->report("resolving") != 0 && $this->report("ticketManager") != $this->user("ign"))
    {
      header("Location: ".$main->link("module/dashboard"));
    }
  }
}

$module = new Module;
$module->checkSession();
$module->checkKey();
$module->checkUser();
?>
<html>
  <head>
    <title><?php echo $main->setting("website_name")." / Report"; ?></title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="shortcut icon" href="<?php echo $main->link($main->setting("website_favicon")); ?>">
    <!-- Browser -->
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta content="mranonymusz" name="author">
    <meta property="og:site_name" content="<?php echo $main->setting("website_name"); ?>">
    <meta property="og:title" content="<?php echo $main->setting("website_name")." / Report"; ?>">
    <meta property="og:description" content="<?php echo $main->setting("website_description"); ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?php echo $main->link($main->setting("website_image")); ?>">
    <meta property="og:url" content="<?php echo $main->link('module/report/'.$_GET['id']); ?>">
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
        <li class="breadcrumb-item"><a href="<?php echo $main->link('module/dashboard'); ?>">Home</a></li>
        <li class="breadcrumb-item active">Report</li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo number_format($_GET['id'], 0, ',', ' '); ?></li>
      </ol>
      <!-- Breadcrumb -->
      <div class="container">
        <div class="row">
          <!-- Report info -->
          <div class="col-md-6">
            <div class="portlet" style="margin-bottom: 15px">
              <div class="portlet-header">
                <h4 class="portlet-title"><i class="fas fa-info-circle"></i> Report Informations</h4>
              </div>
              <div class="portlet-body">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <tr>
                      <td>Reported: </td>
                      <td><?php echo $module->report("reported"); ?></td>
                    </tr>
                    <tr>
                      <td>Reporter: </td>
                      <td><?php echo $module->report("reporter"); ?></td>
                    </tr>
                    <tr>
                      <td>Section: </td>
                      <td><?php echo $module->report("section"); ?></td>
                    </tr>
                    <tr>
                      <td>SubSection: </td>
                      <td><?php echo $module->report("subSection"); ?></td>
                    </tr>
                    <tr>
                      <td>World: </td>
                      <td><?php echo $module->report("world"); ?></td>
                    </tr>
                    <tr>
                      <td>Coordinates: </td>
                      <td><?php echo "X: ".round($module->report("x"), 2)." / Y: ".round($module->report("y"), 2)." / Z: ".round($module->report("z"), 2); ?></td>
                    </tr>
                    <tr>
                      <td>Status: </td>
                      <td>
                        <?php
                        if($module->report("open") == 1)
                        {
                          echo "<span class='badge badge-success'>Open</span>";
                        }
                        else
                        {
                          echo "<span class='badge badge-danger'>Closed</span>";
                        }
                        ?>
                      </td>
                    </tr>
                    <?php 
                    if($module->report("ticketManager") != "none")
                    {
                    ?>
                    <tr>
                      <td>TicketManager: </td>
                      <td><?php echo $module->report("ticketManager"); ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                    <tr>
                      <td>ServerName: </td>
                      <td><?php echo $module->report("serverName"); ?></td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="portlet">
              <div class="portlet-header">
                <h4 class="portlet-title"><i class="fas fa-cog"></i> Actions</h4>
              </div>
              <div class="portlet-body">
                <?php
                if($module->report("ticketManager") == "none")
                {
                  if($module->report("open") == 1)
                  {
                    ?>
                    <script type="text/javascript">
                    $(function() {
                      $('#take_report').click(function() {
                        var id = <?php echo $_GET['id']; ?>;

                        $('#load_tr').load('<?php echo $main->link("inc/actions/take_report.php"); ?>', {
                          method: "post",
                          id: id
                        });
                      });
                    });
                    </script>
                    <button class="btn btn-success btn-lg btn-block" id="take_report">
                      <i class="fas fa-check-circle"></i> Take Report
                    </button>
                    <div id="load_tr"></div>
                    <?php
                  }
                  else
                  {
                    if($module->user("is_admin") == 1)
                    {
                      ?>
                      <script type="text/javascript">
                      $(function() {
                        $('#take_report').click(function() {
                          var id = <?php echo $_GET['id']; ?>;

                          $('#load_tr').load('<?php echo $main->link("inc/actions/take_report.php"); ?>', {
                            method: "post",
                            id: id
                          });
                        });
                      });
                      </script>
                      <button class="btn btn-success btn-lg btn-block" id="take_report">
                        <i class="fas fa-check-circle"></i> Take Report
                      </button>
                      <?php
                    }
                  }
                }
                else
                {
                  if($module->report("ticketManager") == $module->user("ign"))
                  {
                    ?>
                    <script type="text/javascript">
                    $(function() {
                      $('#er_submit').click(function() {
                        var id = "<?php echo $_GET['id'] ?>";
                        var oac = $('[name="oac"]').val();
                        var ire = $('[name="ire"]').val();
                        var hr = $('[name="how_resolved"]').val();
                        var str_lenght;

                        $('#er_output').load('<?php echo $main->link('inc/actions/edit_report.php') ?>', {
                          method: "post",
                          id: id,
                          open: oac,
                          resolving: ire,
                          how_resolved: hr
                        });
                      });

                      $('#edit_report').click(function() {
                        $('#er_modal').modal({
                          show: true,
                          keyboard: false,
                          backdrop: false
                        });
                      });

                      $('#er_modal > .modal-dialog > .modal-content').draggable({
                        containment: "body",
                        cursor: "move"
                      });

                      // Word counter
                      str_lenght = jQuery('[name="how_resolved"]').val().length;
                      
                      if(str_lenght >= 200)
                      {
                        if(!$('.wc').hasClass('text-danger'))
                        {
                          $('.wc').addClass('text-danger');
                        }
                      }

                      $('#wc_output').text(str_lenght);

                      $('[name="how_resolved"]').keyup(function() {
                        str_lenght = jQuery('[name="how_resolved"]').val().length;

                        if(str_lenght >= 200)
                        {
                          if(!$('.wc').hasClass('text-danger'))
                          {
                            $('.wc').addClass('text-danger');
                          }
                        }
                        else if(str_lenght < 200)
                        {
                          if($('.wc').hasClass('text-danger'))
                          {
                            $('.wc').removeClass('text-danger');
                          }
                        }

                        $('#wc_output').text(str_lenght);
                      });
                    });
                    </script>
                    <button class="btn btn-info btn-lg btn-block" id="edit_report">
                      <i class="fas fa-edit"></i> Edit Report
                    </button>
                    <div class="modal fade" id="er_modal" tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">
                              <i class="fas fa-edit"></i> Edit Report
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <div class="alert alert-info" role="alert">
                              If you want the report to be resolved correctly set "<b>open</b>" to "<b>close</b>" and "<b>resolving</b>" to "<b>no</b>".
                            </div>
                            <form action="" method="post" id="er_form">
                              <p class="title">Open/Close report ?</p>
                              <select name="oac" class="form-control">
                                <option value="1" <?php if($module->report("open") == 1) { echo "selected"; } ?>>Open</option>
                                <option value="2" <?php if($module->report("open") == 0) { echo "selected"; } ?>>Close</option>
                              </select>
                              <p class="title">Is resolving ?</p>
                              <select name="ire" class="form-control">
                                <option value="1">Yes</option>
                                <option value="2" selected>No</option>
                              </select>
                              <hr>
                              <p class="title">How the report was resolved ?</p>
                              <textarea name="how_resolved" class="form-control" maxlength="200" placeholder="Please tell us about how you resolved the report."><?php if($module->report("howResolved") != "none") { echo $module->report("howResolved"); } ?></textarea>
                              <p class="wc"><span id="wc_output">0</span>/200</p>
                            </form>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-success" id="er_submit">
                              <i class="fas fa-save"></i> Save
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div id="er_output"></div>
                    <?php
                  }
                  else
                  {
                    if($module->user("is_admin") == 1)
                    {
                      ?>
                      <script type="text/javascript">
                      $(function() {
                        $('#er_submit').click(function() {
                          var id = "<?php echo $_GET['id'] ?>";
                          var oac = $('[name="oac"]').val();
                          var ire = $('[name="ire"]').val();
                          var hr = $('[name="how_resolved"]').val();
                          var str_lenght;

                          $('#er_output').load('<?php echo $main->link('inc/actions/edit_report.php') ?>', {
                            method: "post",
                            id: id,
                            open: oac,
                            resolving: ire,
                            how_resolved: hr
                          });
                        });

                        $('#edit_report').click(function() {
                          $('#er_modal').modal({
                            show: true,
                            keyboard: false,
                            backdrop: false
                          });
                        });

                        $('#er_modal > .modal-dialog > .modal-content').draggable({
                          containment: "body",
                          cursor: "move"
                        });

                        // Word counter
                        str_lenght = jQuery('[name="how_resolved"]').val().length;
                        
                        if(str_lenght >= 200)
                        {
                          if(!$('.wc').hasClass('text-danger'))
                          {
                            $('.wc').addClass('text-danger');
                          }
                        }

                        $('#wc_output').text(str_lenght);

                        $('[name="how_resolved"]').keyup(function() {
                          str_lenght = jQuery('[name="how_resolved"]').val().length;

                          if(str_lenght >= 200)
                          {
                            if(!$('.wc').hasClass('text-danger'))
                            {
                              $('.wc').addClass('text-danger');
                            }
                          }
                          else if(str_lenght < 200)
                          {
                            if($('.wc').hasClass('text-danger'))
                            {
                              $('.wc').removeClass('text-danger');
                            }
                          }

                          $('#wc_output').text(str_lenght);
                        });
                      });
                      </script>
                      <button class="btn btn-info btn-lg btn-block" id="edit_report">
                        <i class="fas fa-edit"></i> Edit Report
                      </button>
                      <div class="modal fade" id="er_modal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">
                                <i class="fas fa-edit"></i> Edit Report
                              </h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <div class="alert alert-info" role="alert">
                                If you want the report to be resolved correctly set "<b>open</b>" to "<b>close</b>" and "<b>resolving</b>" to "<b>no</b>".
                              </div>
                              <form action="" method="post" id="er_form">
                                <p class="title">Open/Close report ?</p>
                                <select name="oac" class="form-control">
                                  <option value="1" <?php if($module->report("open") == 1) { echo "selected"; } ?>>Open</option>
                                  <option value="2" <?php if($module->report("open") == 0) { echo "selected"; } ?>>Close</option>
                                </select>
                                <p class="title">Is resolving ?</p>
                                <select name="ire" class="form-control">
                                  <option value="1">Yes</option>
                                  <option value="2" selected>No</option>
                                </select>
                                <hr>
                                <p class="title">How the report was resolved ?</p>
                                <textarea name="how_resolved" class="form-control" maxlength="200" placeholder="Please tell us about how you resolved the report."><?php if($module->report("howResolved") != "none") { echo $module->report("howResolved"); } ?></textarea>
                                <p class="wc"><span id="wc_output">0</span>/200</p>
                              </form>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-success" id="er_submit">
                                <i class="fas fa-save"></i> Save
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div id="er_output"></div>
                      <?php
                    }
                  }
                }
                ?>
                <hr>
                <textarea class="form-control" style="resize: none; height: 115px" disabled><?php echo $module->report("reason"); ?></textarea>
                <?php
                if($module->report("howResolved") != "none")
                {
                ?>
                <hr>
                <textarea class="form-control" style="resize: none; height: 115px" disabled><?php echo $module->report("howResolved"); ?></textarea>
                <?php
                }
                ?>
              </div>
            </div>
          </div>
          <!-- Report info -->
        </div>
      </div>
      <div style=" background-color: transparent; color: transparent; height: 300px; width: 100%"></div>
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
    });
    </script>
    <!-- JS -->
  </body>
</html>
<?php
ob_end_flush();
?>