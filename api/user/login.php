<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../../define.php';
include_once '../config/database.php';
include_once '../classes/user.php';

$database   =   new Database();
$db         =   $database->getConnection();
$user       =   new User($db);
$login      =   __post('login');
$password   =   __post('password');
$host       =   DB_HOST;
$user       =   DB_USER;
$pass       =   DB_PASS;
$db         =   DB_BASE;
$conn       =   mysqli_connect($host, $user, $pass, $db);

$query = "SELECT * FROM db_mdd_renters WHERE login='$login' AND password='$password' ";

$get_login = Q("SELECT * FROM `#_mdd_renters` WHERE `login`=?s AND `password`= ?s", array($login, $password))->row();

$username_login = $get_login['id'];

$result = mysqli_query($conn,$query);

if (mysqli_num_rows($result) == 1) {
  $_SESSION['authorization'] = true;
  $_SESSION['user_id'] = $username_login;

  if($get_login['is_admin'] == 1) {
    $_SESSION['admin'] = 1;
    echo json_encode(array('result' => 1, 'admin' => 1, 'id' => $get_login['id']));
  } else {
    $_SESSION['admin'] = 0;
    echo json_encode(array('result' => 1, 'admin' => 0, 'id' => $username_login));
  }	

} else {
  echo json_encode(array('result' => 0));
}

$conn->close();