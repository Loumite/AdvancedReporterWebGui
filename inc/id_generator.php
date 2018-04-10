<?php
error_reporting(E_WARNING | E_ERROR | E_PARSE);
header('Content-type: text/html;charset=utf-8');

class idGenerator
{
  private $_prefix;
  private $_suffix;

  public function generateId($_prefix)
  {
    $this->_prefix = $_prefix;

    $explode = explode('.', uniqid('', true));
    $this->_suffix = $explode[0].$explode[1];

    return $this->_prefix."_".str_shuffle($this->_suffix);
  }
}