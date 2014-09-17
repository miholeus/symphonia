<?php
define('PHPTHUMB_DIRECTORY', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'phpthumb' . DIRECTORY_SEPARATOR);

require_once(PHPTHUMB_DIRECTORY . 'ThumbLib.inc.php');

class PhpThumbAdapter
{
  public function __construct(){}
  public function create($path)
  {
    $thumb = PhpThumbFactory::create($path);
    return $thumb;
  }
}
?>