<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if (isset($_POST['month']) && isset($_POST['year']) && isset($_POST['amount']) && isset($_POST['summa']) && isset($_POST['type'])) {
  O('_mdd_communal')->create(array(
    'month'   => __post('month'),
    'year'    => __post('year'),
    'amount'  => __post('amount'),
    'summa'   => __post('summa'),
    'type'    => __post('type'),
  ));
  echo json_encode(array('result' => 1));
} else {
  echo json_encode(array('result' => 1));
}






