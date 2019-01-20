<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../define.php'; 
include_once '../config/database.php';

// подключаемся к БД
$database = new Database();
$db = $database->getConnection(); 

// получаем ID последнего номера счёта
$last_invoice = Q("SELECT MAX(`id`) as `number` FROM `#_mdd_invoice`")->row('number');

// получаем последний номер счёта
$last_invoice_number = Q("SELECT `invoice_number` FROM `#_mdd_invoice` WHERE `id` = ?i", array($last_invoice))->row('invoice_number');

// прибавляем к нему единицу
$number_schet = $last_invoice_number + 1;

// если передаём "начать нумерацию с ..", то устанавливаем это значение
isset($_POST['from_first']) ? $number_schet = $_POST['from_first_number'] : '';

// валидируем поля
if (!empty($_POST['renter']) && !empty($_POST['date']) && !empty($_POST['year']) && !empty($_POST['month'])){

  // берём индекс, для получения нужных данных из массива POST
  $index = 0;

  foreach ($_POST['renter'] as $key => $value) {

    // получаем массив с цифровым значение месяца, а так же кол-во дней в месяце из функции (fn.inc.php)
    $month = getMonthString($_POST['month']);

    $contracts_for_schet = Q("SELECT * from `#_mdd_contracts` as `contract` WHERE `contract`.`renter` = ?i", array($_POST['renter'][$index]))->row();
    $status = 1;
    $renter = Q("SELECT * from `#_mdd_renters` as `renter` WHERE `renter`.`id` = ?i", array($value))->row();
    $rest 	= floatval($_POST['period_sum'][$index]);
    // изменяем баланс
    // сумма по счету
    $period_sum = $rest;
    // ID арендодателя
    $renter_id = $_POST['renter'][$index];
    // ID текущего договора 
    $contract_id = $_POST['summa_id'][$index];


    $start_arenda = explode('.', $contracts_for_schet['start_arenda']); // Парсим дату начала аренды из договора

    // Проверяем совпадают ли месяц начала аренды (меньше или равно) в договоре с месяцем выставляемого счета и равен ли год
    if (($start_arenda[1] == $month['month_number']) && ($start_arenda[2] == $_POST['year'])){
      // Если да, и день начала аренды ревен 1 то колличество в счете = 1	
      if ($start_arenda[0] == '01') {
        $amount = 1;
      } else {  // Если день начала аренды больше 1, то колличество дней аренды в текущем месяце в счете вычисляется
        $days_arenda = $month['days'] - $start_arenda[0] + 1;
        $amount = $days_arenda / $month['days'];			
      }
    }
    else {
      $amount = 1;
    } 
              
    // считаем сумму и остаток исходя из данных
    $summa = $rest = $rest * $amount;
    $summa = number_format($summa, 2, '.', ''); // форматируем
    $period_balance = $summa;

    //находим текущий баланс и считаем разницу между текущим и суммой по счету
    $balance = Q("SELECT `balance` FROM `#_mdd_renters` WHERE `id` = ?i",array($renter_id))->row('balance');
    $contract_balance = Q("SELECT `balance` FROM `#_mdd_contracts` WHERE `id` = ?i", array($contract_id))->row('balance');
    // ихсодные балансы договора и арендодателя для апдейта в будущем
    $starting_balance = Q("SELECT `balance` FROM `#_mdd_renters` WHERE `id` = ?i",array($renter_id))->row('balance');
    $contract_str_balance = Q("SELECT `balance`	FROM `#_mdd_contracts` WHERE `id` = ?i", array($contract_id))->row('balance');
    // считаем баланс
    $balance -= $period_balance;
    $contract_balance -= $period_balance;
    $balance = number_format($balance,2,".","");
    $contract_balance = number_format($contract_balance,2,".","");

    // апдейтим баланс у арендодателя
    $sql = "UPDATE `db_mdd_renters` SET `balance` = '$balance' WHERE `db_mdd_renters`.`id` = '$renter_id'";
    // апдейтим баланс у договора
    $update_contract_balance = "UPDATE `db_mdd_contracts` SET `balance` = '$contract_balance' WHERE `db_mdd_contracts`.`id` = '$contract_id'";
    
    $db->query($sql);
    $db->query($update_contract_balance);
    
    // еще раз првоеряем баланс, если положительный то..
    if ($starting_balance > 0) {
      // записываем в остаток разницу счета и баланса
      $rest = $rest - $starting_balance;
      // если разница меньше нуля, значит баланс покрывает счет полность
      // => делаем остаток 0, меняем статус на 0, и пересчитываем баланс
      if ($rest < 0) {
        $rest = 0;
        $status = 0;
        // вычитаем из баланса всю сумму счета
        $starting_balance -= $period_balance;
        $contract_str_balance -= $period_balance;
        $starting_balance = number_format($starting_balance,2,".","");
        $contract_str_balance = number_format($contract_str_balance,2,".","");
        // меняем баланс у арендодателя
        $update_balance = "UPDATE `db_mdd_renters` SET `balance` = '$starting_balance' WHERE `db_mdd_renters`.`id` = '$renter_id'";
        $update_ctr_balance = "UPDATE `db_mdd_contracts` SET `balance` = '$contract_str_balance' WHERE `db_mdd_contracts`.`id` = '$contract_id'";
        $db->query($update_balance);
        $db->query($update_ctr_balance);
      }
    }
      
    $lastdaydate  = $_POST['year'] . '-' . $month['month_number'] . '-' . $month['days'];
    
    $discount = Q("SELECT `discoint` FROM `#_mdd_contracts` WHERE `id` = ?i",array($_POST['summa_id'][$index]))->row('discoint');
    
    // Добавляем запись в таблицу Счета
    O('_mdd_invoice')->create(array(
      'renter' => $renter['full_name'],
      'invoice_date' => $_POST['date'],
      's:period_year' => $_POST['year'],
      's:period_month' => $month['month_number'],
      'contract_id' => $_POST['summa_id'][$index],
      'invoice_number' => $number_schet,
      'status' => $status,
      'payment_info' => '',
      'summa'	=> $summa,
      'amount' => $amount,
      'discount' => $discount,
      'rest' => $rest, 
      'number_index' => 0,
      'schet_id' => $number_schet,
      'sf_number' => $number_schet,
      'akt_id' => $number_schet,
      'akt_number' => $number_schet,
      'akt_date' => $lastdaydate,
      'sf_date' => $lastdaydate						
    ));	

    // баланс в таблице баланса
    $contract_name = Q("SELECT `number`, `ground`, `balance` FROM `#_mdd_contracts` WHERE `id` = ?i", array($_POST['summa_id'][$index]))->row(); 
    // записываем данные в таблицу балансов
    O('_mdd_balance')->create(array(
      'renter_id' => $renter['id'],
      'contract_id' => $_POST['summa_id'][$index],
      'balance' => $contract_name['balance'],
      'ground' => 'schet',
      'contract' => $contract_name['number'],
      'date' => $_POST['date'],
      'renter' => $renter['full_name'],
      'ground_id' => $number_schet,	
      'summa' => $summa,
    ));	

    // плюсуем номер счёта и индекс
    $number_schet++;	
    $index++;
    // если индекс равен длинна массива, то индекс равен 0
    $index == count($_POST['summa_id']) ? $index = 0 : '';
    	

  }					
}
				
echo json_encode(array('result' => 1));
