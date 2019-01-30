<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/database.php';
include_once '../classes/balance.php';

$database = new Database();
$db = $database->getConnection();

$balances = new Balance($db);
 
$stmt = $balances->read();

function renameGround($string) {
  switch($string) {
    case 'peni':
      $string = 'пени';
      break;
    case 'payment':
      $string = 'оплата';
      break;
    case 'schet':
      $string = 'счёт';
      break;
    case 'peni-payment':
      $string = 'оплата пени';
      break;
    default:
      $string = 'нет такого типа';
  }
  return $string;
}

for ($i = 0; $i < count($stmt); $i++){
  $stmt[$i]['ground'] = renameGround($stmt[$i]['ground']);
}

echo json_encode($stmt);