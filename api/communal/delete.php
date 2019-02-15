<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

# получаем id из POST
$id = __post('id');

if(isset($id)) {
  $sql = "DELETE FROM `db_mdd_communal` WHERE `id`= '$id'";
  $db->query($sql);

  echo json_encode(array('result' => 1));
} else {
  echo json_encode(array('result' => 0));
} 