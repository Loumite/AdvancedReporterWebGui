<?php
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

/*
  >> Alternative hashing: https://github.com/CypherX/xAuth/wiki/Password-Hashing
*/

class PasswordEncrypter
{
  private $_password;
  private $_salt;
  private $_lenght;

  private function randomStringGenerate($_lenght)
  {
    $this->_lenght = $_lenght;

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $this->_lenght; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
  }

  public function encryptPassword($_password)
  {
    $this->_password = $_password;
    $this->_salt = $this->randomStringGenerate(16);

    $enc_pw = '$SHA$'.$this->_salt.'$'.hash("sha256", hash("sha256", $_password).$this->_salt);

    return $enc_pw;
  }
}