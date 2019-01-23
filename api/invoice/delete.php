<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../../define.php';
include_once '../config/database.php';
include_once '../classes/invoice.php';

$database = new Database();
$db = $database->getConnection();

$invoices = new Invoice($db);

// получаем данные из POST
$invoice = __post('invoice_id');
$contract = __post('contract');

if($invoice != '' && $contract != '') {

  // находим арендодателя, которому был выставлен счёт
  $renter = Q("SELECT `renter` 	FROM `#_mdd_invoice` WHERE `invoice_number` = ?s",array($invoice))->row('renter');

  // находим сумму в счёте
  $sum = Q("SELECT `summa` 	FROM `#_mdd_invoice` WHERE `invoice_number` = ?s",array($invoice))->row('summa');

  // находим общий баланс и баланс по договору арендодателя
  $contract_balance = Q("SELECT `balance` FROM `#_mdd_contracts` WHERE `id` = ?s",array($contract))->row('balance');
  $renter_balance = Q("SELECT `balance` FROM `#_mdd_renters` WHERE `full_name` = ?s",array($renter))->row('balance');

  // так как удаляем счёт, то нам нужно приплюсовать сумму 
  $contract_balance += $sum;
  $renter_balance += $sum;

  // удаляем из БД
  $sql = "DELETE FROM `db_mdd_invoice` WHERE `id`= '$invoice'";
  $db->query($sql);

  // удаляем запись из таблицы балансов
  $id = Q("SELECT `id` FROM `#_mdd_balance` WHERE `ground_id` = '$invoice'", array())->row('id');
  $delete_balance = "DELETE FROM `db_mdd_balance` WHERE `id` = '$id'";
  $db->query($delete_balance);

  // удаляем сам счёт
  $delete_invoice = "DELETE FROM `db_mdd_invoice` WHERE `invoice_number` = '$invoice'";
  $db->query($delete_invoice);

  // находим все последующие записи в балансах (если есть)
  $balance_array = Q("SELECT `balance`,`id` FROM `#_mdd_balance` 
    WHERE `contract_id` = '$contract' AND `id` > '$id'",array())->all();

  // для каждого нам нужно обновить баланс
  for ($i = 0; $i < count($balance_array); $i++) {
    $current_id = $balance_array[$i]['id'];
    $current_sum = $balance_array[$i]['balance'] + $sum;
    $update_array_balance = "UPDATE `db_mdd_balance` SET `balance` = '$current_sum' WHERE `db_mdd_balance`.`id` = '$current_id'";
    $db->query($update_array_balance);
  }

  // обновляем итоговый баланс
  $update = "UPDATE `db_mdd_renters` SET `balance` = '$renter_balance' WHERE `db_mdd_renters`.`full_name` = '$renter'";
  $update_contract = "UPDATE `db_mdd_contracts` SET `balance` = '$contract_balance' WHERE `db_mdd_contracts`.`id` = '$contract'";
  $db->query($update_contract);
  $db->query($update);

  echo json_encode(array('result' => 1));
} else {
  echo json_encode(array('result' => 0));
}
