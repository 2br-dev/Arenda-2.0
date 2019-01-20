<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/database.php';
include_once '../classes/invoice.php';

$database = new Database();
$db = $database->getConnection();

$invoices = new Invoice($db);

$renter = __post('renter');
$month = __post('month');
$year = __post('year');

if ($year != '') {

  if ($renter != '' && $renter != '0' && $month != '00') {
    $stmt = $invoices->read_renter_year_month($renter, $year, $month);
  } elseif ($renter != '' && $renter != '0') {
    $stmt = $invoices->read_renter_year($renter, $year);
  } elseif ($month != '00') {
    $stmt = $invoices->read_year_month($year, $month);
  } else {
    $stmt = $invoices->read_year($year);
  }

  echo json_encode(array('result' => 1, 'invoices' => $stmt));

}
