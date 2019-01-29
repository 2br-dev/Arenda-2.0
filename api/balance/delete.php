<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../../define.php';
include_once '../config/database.php';
include_once '../classes/balance.php';

$database = new Database();
$db = $database->getConnection();

$invoices = new Balance($db);

# получаем данные из POST
$id                 = __post('id');
$renter_short_name  = __post('renter');
$contract_number    = __post('number');
$ground             = __post('ground');
$ground_id          = __post('ground_id');
$summa              = __post('summa');

if(!isset($invoice)) {

  # находим общий баланс и баланс по договору арендодателя
  $contract_balance = Q("SELECT `balance` FROM `#_mdd_contracts` WHERE `number` = ?s",array($contract_number))->row('balance');
  $renter_balance = Q("SELECT `balance` FROM `#_mdd_renters` WHERE `short_name` = ?s",array($renter_short_name))->row('balance');

  # так как удаляем счёт, то нам нужно приплюсовать сумму 
  $contract_balance += $summa;
  $renter_balance += $summa;

  if($ground == 'счёт') {
    # удаляем из БД
    $sql = "DELETE FROM `db_mdd_invoice` WHERE `invoice_number`= '$ground_id'";
    $db->query($sql);
  }

  if($ground == 'оплата') {
    $sql = "DELETE FROM `db_mdd_payments` WHERE `id`= '$ground_id'";
    $db->query($sql);
  }

  # удаляем запись из таблицы балансов
  $delete_balance = "DELETE FROM `db_mdd_balance` WHERE `id` = '$id'";
  $db->query($delete_balance);

  # находим все последующие записи в балансах (если есть)
  $balance_array = Q("SELECT `balance`,`id` FROM `#_mdd_balance` 
    WHERE `contract` = '$contract_number' AND `id` > '$id'",array())->all();

  # для каждого нам нужно обновить баланс
  for ($i = 0; $i < count($balance_array); $i++) {
    $current_id = $balance_array[$i]['id'];
    $current_sum = $balance_array[$i]['balance'] + $summa;
    $update_array_balance = "UPDATE `db_mdd_balance` SET `balance` = '$current_sum' WHERE `db_mdd_balance`.`id` = '$current_id'";
    $db->query($update_array_balance);
  }

  # обновляем итоговый баланс
  $update = "UPDATE `db_mdd_renters` SET `balance` = '$renter_balance' WHERE `db_mdd_renters`.`short_name` = '$renter_short_name'";
  $update_contract = "UPDATE `db_mdd_contracts` SET `balance` = '$contract_balance' WHERE `db_mdd_contracts`.`number` = '$contract_number'";
  $db->query($update_contract);
  $db->query($update); 

  echo json_encode(array('result' => 1));
} else {
  echo json_encode(array('result' => 0));
} 