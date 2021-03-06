<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../define.php';
include_once '../config/database.php';
include_once '../classes/peni.php';
include_once '../classes/invoice.php';
include_once './compare.php';

$database = new Database();
$db = $database->getConnection();
$Peni = new Peni($db);
$Invoice = new Invoice($db);

$summa           = floatval(__post('summa'));
$date            = __post('date');
$number          = __post('number');
$renter_id       = __post('renter_id');
$renter_name     = __post('renter_name');
$renter_document = __post('renter_document');
$id              = __post('contract_id');


// проверяем был ли оплачен счёт повторно
if(!comparePayment($summa, $date, $number, $renter_name, $renter_document, $id)) {
  echo json_encode(array('payed' => true));
  die;
}

$allinvoices     = $Invoice->read_active($id);
$invoices        = array();

foreach($allinvoices as $invoice) {
  array_push($invoices, $invoice['id']);
}

if (isset($summa) && isset($date) && isset($number) && isset($renter_id) && isset($renter_name) 
  && isset($renter_document) && isset($id) && isset($invoices)) {

  // парсим необходимые даты
  $date = explode('-', $date);

  // получаем информация о пени по договору (сумма и процент)
  $peni_info = $Peni->get_info($id); 
  $peni_summa = $peni_info['summa'];
  $peni_percent = $peni_info['peni'] * 0.01;

  // полное наименование арендатора
  $renter_full_name = Q("SELECT `full_name` FROM `#_mdd_renters` WHERE `id` = ?i", array($renter_id))->row('full_name');

  // работаем с пени
  $Peni->pay(__post('date'), $date, $invoices, $renter_document, $summa, $renter_id, $id, $renter_full_name, $db, $peni_percent);

  
  // записываем оплату
  O('_mdd_payments')->create(array(
    'renter_name' => $renter_name,
    'date' => __post('date'),
    'summa' => $summa,
    'document' => $id,
    'payment_info' => $renter_document,
    'invoices' => implode(",", $invoices),
    'rest' => __post('number'),
  ));
   
  // берем общий баланс арендодателя и баланс контракта	
  $balance = floatval(Q("SELECT `balance` FROM `#_mdd_renters` WHERE `id` = ?i", array($renter_id))->row('balance'));
  $contract_balance = floatval(Q("SELECT `balance` FROM `#_mdd_contracts` WHERE `id` = ?i", array($id))->row('balance'));
  
  // прибавляем им сумму
  $balance += $summa;
  $contract_balance += $summa;
  $contract_balance = str_replace(",",".", $contract_balance);
  $balance = str_replace(",",".", $balance);

  // запросы на изменение баланса
  $sql_balance = "UPDATE `db_mdd_renters` SET `balance` = '$balance' WHERE `db_mdd_renters`.`id` = '$renter_id'";
  $sql_cont_balance = "UPDATE `db_mdd_contracts` SET `balance` = '$contract_balance' WHERE `db_mdd_contracts`.`id` = '$id'";

  # получаем последний ID в оплатах
  $payment_id = Q("SELECT MAX(`id`) as `max_id` FROM `#_mdd_payments`")->row('max_id');

  // меняем балансы
  $db->query($sql_balance);
  $db->query($sql_cont_balance);

  // обновляем баланс по факту оплаты
  O('_mdd_balance')->create(array(
    'renter_id' => $renter_id,
    'contract_id' => $id,
    'balance' => $contract_balance,
    'ground' => 'payment',
    'contract' => $renter_document,
    'date' => __post('date'),
    'renter' => $renter_full_name,
    'ground_id' => implode(",", $invoices),
    'summa' => $summa,
  ));

  echo json_encode(array('result'=> 1));
} 

  else 

{
  echo json_encode(array('result'=> 0));
}
 

