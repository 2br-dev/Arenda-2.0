<?php
$t1 = microtime(true);

require 'define.php';

$app = new Fastest\Core\App();

$app->terminate($_SERVER);

if(strtok($_SERVER["REQUEST_URI"],'?') == '/schet-pechatnaya-forma') {
  die;
}

if(!isset($_SESSION['authorization']) && $_SERVER['REQUEST_URI'] != '/login') {
  header('location:/login');
};   

if(isset($_SESSION['user_id']) && $_SERVER['REQUEST_URI'] != '/account') {
  if($_SESSION['admin'] != 1) {
    header('location:/account');
  }
}


# Load time
// $app->logger($t1);
