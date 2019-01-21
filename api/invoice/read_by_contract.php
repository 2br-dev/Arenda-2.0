<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/database.php';
include_once '../classes/invoice.php';

$database = new Database();
$db = $database->getConnection();

$invoices = new Invoice($db);

$id = __post('id');

$stmt = $invoices->read_by_contract($id);
  
echo json_encode($stmt);

