<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/database.php';
include_once '../classes/room.php';

$database = new Database();
$db = $database->getConnection();

$rooms = new Room($db);
 
$stmt = $rooms->read();

echo json_encode($stmt);