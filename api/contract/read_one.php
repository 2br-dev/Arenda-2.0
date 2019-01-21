<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/database.php';
include_once '../classes/contract.php';

$database = new Database();
$db = $database->getConnection();

$contracts = new Contract($db);

$id = __post('id');

$stmt = $contracts->read_one($id);
 
echo json_encode($stmt);
