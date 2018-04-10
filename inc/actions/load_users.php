<?php
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

require_once("../database.php");

class LoadUsers extends Database
{
  private $_secret_key;
  private $_setting;
  private $_user;
  private $_id;

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

  private function setting($_setting)
  {
    $dbh = new Database;

    $this->_setting = $_setting;

    $select_setting = $dbh->connect()->query("SELECT value FROM settings WHERE name='$this->_setting'");
    $display_setting = $select_setting->fetch_array();

    return $display_setting['value'];
  }

  private function user($_user)
  {
    $dbh = new Database;

    if($this->checkSession() == true)
    {
      $this->_secret_key = mysqli_real_escape_string($dbh->connect(), $_COOKIE['secret_key']);
      $this->_secret_key = htmlspecialchars($this->_secret_key);
      $this->_secret_key = trim($this->_secret_key);

      $this->_user = $_user;

      $select_user = $dbh->connect()->query("SELECT * FROM users WHERE secret_key='$this->_secret_key'");
      $display_user = $select_user->fetch_array();

      return $display_user[$this->_user];
    }
  }

  public function __construct()
  {
    if($this->checkSession() == true)
    {
      if($this->user("is_admin") == 1)
      {
        $dbh = new Database;

        $select_users = $dbh->connect()->query("SELECT * FROM users");
        
        $raw_users = array();
        $users = array();

        while($display_users = $select_users->fetch_array())
        {
          if($display_users['secret_key'] != $_COOKIE['secret_key'])
          {
            $this->_id = $display_users['id'];
            $this->numb += 1;

            $raw_users[$this->_id]['id'] = $this->numb;
            
            if($display_users['avatar'] == "default")
            {
              $raw_users[$this->_id]['avatar'] = "<img src='".$this->setting("website_scheme").$this->setting("website_domain")."/".$this->setting("default_avatar")."' class='user_avatar' draggable='false' />";
            }
            else if($display_users['avatar'] == "mc")
            {
              require_once("../mojang-api.php");

              $uuid = MojangAPI::getUuid($display_users['ign']);

              $raw_users[$this->_id]['avatar'] = "<img src='".MojangAPI::embedImage(MojangAPI::getPlayerHead($uuid))."' class='user_avatar' draggable='false' />";
            }
            else
            {
              $raw_users[$this->_id]['avatar'] = "<img src='".$display_users['avatar']."' class='user_avatar' draggable='false' />";
            }

            $raw_users[$this->_id]['username'] = $display_users['username'];
            $raw_users[$this->_id]['ign'] = $display_users['ign'];
            $raw_users[$this->_id]['email'] = $display_users['email'];

            if($display_users['is_admin'] == 1)
            {
              $raw_users[$this->_id]['rank'] = "<span class='badge badge-danger'>Admin</span>";
            }
            else
            {
              $raw_users[$this->_id]['rank'] = "<span class='badge badge-info'>User</span>";
            }
          }
        }

        foreach($raw_users as $users_2)
        {
          $users[] = $users_2;
        }

        echo json_encode($users);
      }
    }
  }
}

$lu = new LoadUsers;