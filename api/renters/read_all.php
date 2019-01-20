<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/database.php';
include_once '../classes/renters.php';

$database = new Database();
$db = $database->getConnection();

$renters = new Renters($db);

$stmt = $renters->read_all();
 
echo json_encode($stmt);
