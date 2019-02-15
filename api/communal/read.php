<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$services = Q("SELECT * FROM `#_mdd_communal` ORDER BY `id` DESC")->all();

echo json_encode($services);


