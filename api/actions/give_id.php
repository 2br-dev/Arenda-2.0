<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../define.php';
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$allinvoices = Q("SELECT `renter`, `id` FROM `#_mdd_invoice`")->all();


foreach ($allinvoices as $invoice) {
  $renter_id = Q("SELECT `id` FROM `#_mdd_renters` WHERE `full_name` = ?s", array($invoice['renter']))->row('id');
  
  $invoice_id = $invoice['id'];

  $sql = "UPDATE `db_mdd_invoice` SET `db_mdd_invoice`.`renter_id` = '$renter_id' WHERE `db_mdd_invoice`.`id` = '$invoice_id'";
  $db->query($sql);
}

$allinvoices = Q("SELECT `renter_id` FROM `#_mdd_invoice`")->all();

echo json_encode($allinvoices);

