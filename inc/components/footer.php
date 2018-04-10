<?php
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

date_default_timezone_set($main->setting("timezone"));
?>
<div class="footer">
  <div class="left">
    <p><?php echo "&copy;".date('Y')." ".$main->setting("website_name"); ?></p>
  </div>
  <div class="right">
    <ul>
      <li>
        <a href="https://www.spigotmc.org/members/nexgan.157889/" target="_blank">Nexgan</a>
      </li>
      <li>
        <a href="https://github.com/MrAnonymusz" target="_blank">MrAnonymusz</a>
      </li>
    </ul>
  </div>
</div>