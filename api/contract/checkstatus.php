<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../../define.php';
include_once '../classes/contract.php';

$database = new Database();
$db = $database->getConnection();

$contracts = new Contract($db);

$allcontracts = $contracts->read_active();

foreach($allcontracts as $contract) {
  if($contract['end_date'] != '') {
    $id = $contract['id'];
    $milis = strtotime($contract['end_date']) - strtotime(date("Y/m/d"));
    $diff = $milis / (3600 * 24);
    echo $diff.' \n ';
    if ($diff < 0  ) {
      $sql = "UPDATE `db_mdd_contracts` SET `status` = '0' WHERE `db_mdd_contracts`.`id` = '$id'";
    } elseif ($diff > 0 && $diff < 30) {
      $sql = "UPDATE `db_mdd_contracts` SET `status` = '0.5' WHERE `db_mdd_contracts`.`id` = '$id'";
    }
    $db->query($sql);
  }
}

