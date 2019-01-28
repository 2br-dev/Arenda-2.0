<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/database.php';
include_once '../classes/balance.php';

$database = new Database();
$db = $database->getConnection();

$balances = new Balance($db);
 
$stmt = $balances->read();

echo json_encode($stmt);