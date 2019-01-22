<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/database.php';
include_once '../classes/peni.php';

$database = new Database();
$db = $database->getConnection();

$peni = new Peni($db);

$shortname = __post('shortname');

$stmt = $peni->read_by_renter($shortname);
  
echo json_encode($stmt);

