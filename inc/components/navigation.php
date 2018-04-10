<?php
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');
?>
<div class="navigation">
  <div class="res">
    <div class="left">
      <img src="<?php echo $main->link("assets/img/logo.png"); ?>" draggable="false" />
      <p class="title"><?php echo $main->setting("website_name"); ?></p>
    </div>
    <div class="right">
      <button id="toggle-nav">
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </div>
  <div class="menu">
    <div class="top-nav">
      <div class="left">
        <img src="<?php echo $main->link("assets/img/logo.png"); ?>" draggable="false" />
        <p class="title"><?php echo $main->setting("website_name"); ?></p>
      </div>
      <ul class="metismenu right" id="tnr">
        <li class="user">
          <a href="javascript:;" aria-expanded="false">
            <i class="fas fa-user"></i> <?php echo $module->user("username"); ?> <i class="fas fa-caret-down"></i>
          </a>
          <ul class="submenu" aria-expanded="false">
            <li class="text" data-toggle="tooltip" data-placement="top" title="<?php echo $module->user("email"); ?>">
              <i class="fas fa-user-circle"></i> <?php echo $module->user("ign"); ?>
            </li>
            <li class="divider"></li>
            <li>
              <a href="<?php echo $main->link("module/settings"); ?>">
                <i class="fas fa-cog"></i> Settings
              </a>
            </li>
            <?php
            if($module->user("is_admin") == 1)
            {
            ?>
            <li>
              <a href="<?php echo $main->link("module/user-manager"); ?>">
                <i class="fas fa-users"></i> User Manager
              </a>
            </li>
            <?php
            }
            ?>
            <li>
              <a href="https://discord.gg/4SxSuNM" target="_blank">
                <i class="fab fa-discord"></i> Discord
              </a>
            </li>
            <li class="divider"></li>
            <li>
              <a href="<?php echo $main->link("page/logout/").$module->user("secret_key"); ?>">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
    <div class="side-nav">
      <div class="top">
        <?php
        if($module->user("avatar") == "default")
        {
          echo "<img src='".$main->link($main->setting("default_avatar"))."' class='avatar' draggable='false' />";
        }
        else if($module->user("avatar") == "mc")
        {
          require_once("./inc/mojang-api.php");

          $uuid = MojangAPI::getUuid($module->user("ign"));

          echo "<img src='".MojangAPI::embedImage(MojangAPI::getPlayerHead($uuid))."' class='avatar' draggable='false' />";
        }
        else
        {
          echo "<img src='".$module->user("avatar")."' class='avatar' draggable='false' />";
        }
        ?>
        <h2><?php echo $module->user("username"); ?></h2>
        <h5><?php echo $module->user("ign"); ?></h5>
        <?php
        if($module->user("is_admin") == 1)
        {
          echo "<div class='rank bg-danger'><i class='fas fa-user-secret'></i> Admin</div>";
        }
        else
        {
          echo "<div class='rank bg-info'><i class='fas fa-user'></i> User</div>";
        }
        ?>
        <hr>
      </div>
      <ul class="metismenu sn-menu">
        <li>
          <a href="<?php echo $main->link('module/dashboard'); ?>">
            <i class="fas fa-home"></i> Home
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>