<?php
// парсим необходимые даты
$payment_date = explode('-', $_POST['date']);
$payment_year = intval($payment_date[0]);
$payment_month = intval($payment_date[1]);
$payment_day = intval($payment_date[2]);
		// по дефолту значения пени равны нулю
$peni_summa = Q("SELECT `summa` FROM `#_mdd_contracts` WHERE `id` = ?i", array($_POST['contract_id']))->row('summa');
$peni_percent = Q("SELECT `peni` FROM `#_mdd_contracts` WHERE `id` = ?i", array($_POST['contract_id']))->row('peni');
$peni_percent *= 0.01;
		// количество дней в месяце
$number_of_days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

$id = $_POST['contract_id'];
$index = 0;
$renter_id = $_POST['renter_id'];

$servername = DB_HOST;
$username = DB_USER;
$password = DB_PASS;
$dbname = DB_BASE;

		// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$rest_sum = to_money($_POST['summa']);

if (isset($_POST['summa']) && isset($_POST['renter_document'])) {
			// парсим номер договора
  $peni_contract = $_POST['renter_document'];
			// имя арендателя
  $renter_name = Q("SELECT `short_name` FROM `#_mdd_renters` WHERE `id` = ?i", array($_POST['renter_id']))->row('short_name');
			// получаем пени по договору
  $peni_in_contract = Q("SELECT `peni` FROM `#_mdd_contracts` WHERE `id` = ?i", array($id))->row('peni');

			// записываем в переменную сумму
  $period_sum = $_POST['summa'];
  for ($i = 0; $i < count($_POST['invoices']); $i++) {

				// сначала оплатим пени
				// сначала найдем все пени со статусом 1
    $allpeni = Q("SELECT * FROM `#_mdd_peni` WHERE `status` = 1 ORDER BY `id` ASC", array())->all();

				// получаем инвойс номер
				// и дату // и имя
    $invoice = $_POST['invoices'][$i];
    $invoice_date_balance = Q("SELECT * FROM `#_mdd_invoice` WHERE `invoice_number` = $invoice", array())->row('invoice_date');
    $renter_full_name = Q("SELECT `full_name` FROM `#_mdd_renters` WHERE `id` = ?i", array($renter_id))->row('full_name');
    $cur_rest = Q("SELECT `rest`     FROM `#_mdd_invoice` WHERE `invoice_number` = ?i", array($invoice))->row('rest');

				// проходимся циклом по всему массиву пеней и считаем разницу
    if (isset($allpeni)) {
      foreach ($allpeni as $item) {
        $peni_id = $item['id'];
        $peni_rest = $item['rest'];
        $peni_rest = $period_sum - $peni_rest;
					
					// получаем баланс в контракте 
        $contracts = Q("SELECT `number`, `balance` FROM `#_mdd_contracts` WHERE `id` = $id", array())->row();
        $payed_peni = Q("SELECT `peni`, `peni_invoice` FROM `#_mdd_peni` WHERE `id` = '$peni_id'", array())->row();

        if ($peni_rest == 0) {
          $upd_peni_rest = "UPDATE `db_mdd_peni` SET `rest` = 0, `status` = 0  WHERE `id` = '$peni_id'";
          $conn->query($upd_peni_rest);
          $period_sum = 0;

          O('_mdd_balance')->create(array(
            'renter_id' => $_POST['renter_id'],
            'contract_id' => $id,
            'balance' => $contracts['balance'] + $payed_peni['peni'],
            'ground' => 'peni-payment',
            'contract' => $contracts['number'],
            'date' => $invoice_date_balance,
            'renter' => $renter_full_name,
            'ground_id' => $payed_peni['peni_invoice'],
            'summa' => $payed_peni['peni'],
          ));
          $rest_sum -= $payed_peni['peni'];

          $rest_sum <= 0 ? $rest_sum = 0 : '';

          break;

        } elseif ($peni_rest > 0) {
          $upd_peni_rest = "UPDATE `db_mdd_peni` SET `rest` = 0, `status` = 0  WHERE `id` = '$peni_id'";
          $conn->query($upd_peni_rest);
          $period_sum = $peni_rest;
          O('_mdd_balance')->create(array(
            'renter_id' => $_POST['renter_id'],
            'contract_id' => $id,
            'balance' => $contracts['balance'] + $payed_peni['peni'],
            'ground' => 'peni-payment',
            'contract' => $contracts['number'],
            'date' => $invoice_date_balance,
            'renter' => $renter_full_name,
            'ground_id' => $payed_peni['peni_invoice'],
            'summa' => $payed_peni['peni'],
          ));

          $rest_sum -= $payed_peni['peni'];
          $rest_sum <= 0 ? $rest_sum = 0 : '';
          continue;

        } else {
          $period_sum = -1 * $peni_rest;
          $upd_peni_rest = "UPDATE `db_mdd_peni` SET `rest` = '$period_sum', `status` = 1  WHERE `id` = '$peni_id'";
          $conn->query($upd_peni_rest);
          O('_mdd_balance')->create(array(
            'renter_id' => $_POST['renter_id'],
            'contract_id' => $id,
            'balance' => $contracts['balance'] + $payed_peni['peni'],
            'ground' => 'peni-payment',
            'contract' => $contracts['number'],
            'date' => $invoice_date_balance,
            'renter' => $renter_full_name,
            'ground_id' => $payed_peni['peni_invoice'],
            'summa' => $payed_peni['peni'],
          ));

          $rest_sum -= $payed_peni['peni'];
          $rest_sum <= 0 ? $rest_sum = 0 : '';
          break;
        }
      }
    } 

				// получаем день начисления пени
    $start_peni = intval(Q("SELECT `start_peni` FROM `#_mdd_contracts` WHERE `id` = ?i", array($id))->row('start_peni'));

				//парсим дату счета
    $invoice_date = Q("SELECT * FROM `#_mdd_invoice` WHERE `invoice_number` = $invoice", array())->row('akt_date');
    $invoice_date = explode('-', $invoice_date);
    $invoice_month = intval($invoice_date[1]);
    $invoice_year = intval($invoice_date[0]);
			
				// если год четный то в феврале 29 дней
    if (is_int(3019 % $invoice_year / 4)) {
      $number_of_days[1] = 29;
    }
				
				//проверяем дату когда был оплачен счёт
    if (($payment_day <= 5 && $payment_month == $invoice_month && $payment_year == $invoice_year) || ($payment_month < $invoice_month && $payment_year == $invoice_year)) {

      $discount = Q("SELECT `discount` FROM `#_mdd_invoice` WHERE `invoice_number` = ?i", array($invoice))->row('discount');
      $cur_summa = Q("SELECT `summa`    FROM `#_mdd_invoice` WHERE `invoice_number` = ?i", array($invoice))->row('summa');

      $rest = $discount - ($cur_summa - $cur_rest);
      $updated_balance = $cur_summa - $discount;
      $balance = Q("SELECT `balance` FROM `#_mdd_renters` WHERE `id` = ?i", array($renter_id))->row('balance');
      $contract_balance = Q("SELECT `balance` FROM `#_mdd_contracts` WHERE `id` = ?i", array($id))->row('balance');
      $balance += $updated_balance;
      $contract_balance += $updated_balance;

      $upd_summa = "UPDATE `db_mdd_invoice` SET `summa` = $discount WHERE `invoice_number` = $invoice";
      $upd_rest = "UPDATE `db_mdd_invoice` SET `rest` = $rest WHERE `invoice_number` = $invoice";
      $sql_balance = "UPDATE `db_mdd_renters` SET `balance` = $balance WHERE `db_mdd_renters`.`id` = $renter_id";
      $sql_cont_balance = "UPDATE `db_mdd_contracts` SET `balance` = $contract_balance WHERE `db_mdd_contracts`.`id` = $id";

      $conn->query($upd_summa);
      $conn->query($upd_rest);
      $conn->query($sql_balance);
      $conn->query($sql_cont_balance);
    }

    $peni = 0;
    $peni_delay = 0;
    $peni_amount = 0;

				// проверяем дату оплаты на предмет начиления пени
    if ($payment_day >= $start_peni || $payment_month > $invoice_month || $payment_year > $invoice_year) {
      if ($payment_month >= $invoice_month && $payment_year >= $invoice_year) {
        $cur_summa = Q("SELECT `summa`    FROM `#_mdd_invoice` WHERE `invoice_number` = ?i", array($invoice))->row('summa');
        $cur_amount = Q("SELECT `amount`	FROM `#_mdd_invoice` WHERE `invoice_number` = ?i", array($invoice))->row('amount');
        $start_arenda = Q("SELECT `start_arenda` FROM `#_mdd_contracts` WHERE `id` = ?i", array($id))->row('start_arenda');
        $start_arenda = explode('.', $start_arenda);
						
						// если в этом месяце меньше чем полный месяц, то
        $extra_days = 0;
        if ($cur_amount < 1) {
          $extra_days = intval($start_arenda[0]) - $start_peni + 1;
        }				
						// если месяц оплаты старше первого месяца договора, то 
        if ($payment_month >= $start_arenda[1]) {
          $first_month_peni = $start_arenda[0] + 9;
          if ($first_month_peni > $number_of_days[$start_arenda[1] - 1]) {
            $first_month_peni = 0;
          }
        }
        if (isset($first_month_peni) && $invoice_month == $start_arenda[1]) {
          $start_peni = $first_month_peni;
        }

        $dif_days = $number_of_days[$payment_month - 1] - $payment_day - 1;
						// если месяц оплаты не совпадает (больше), то..
        if ($payment_month > $invoice_month || $payment_year > $invoice_year) {
							// считаем разницу в месяцах 
          $dif_month = $payment_month - $invoice_month; // вычит. месяц счета из месяца оплаты 
							// циклом собераем все дни в нужных месяцах :
          $all_days = 0;

          $current_month = $payment_month;
          for ($it = 1; $it <= $dif_month + 1; $it++) {
            $current_month_index = $current_month - $it;
            if ($current_month_index < 0) {
              $current_month_index = 11;
              $current_month = 11;
            }
            $all_days = $all_days + $number_of_days[$current_month_index];
          }

          $peni_delay = $all_days - $dif_days - $start_peni;
        } 
						// иначе (месяц тот же) просто разница между кол-вом дней в этом месяце и началой пени в договоре ($start_peni)
        else {
          $peni_delay = $number_of_days[$payment_month - 1] - $start_peni - $dif_days;  // +1 так как день включительно
        }
      }
    }

    if ($peni_delay < 0) {
      $peni_delay = 0;
    } 


					// за этот месяц будет начислять пени и мы можем подсчитать
    $peni = number_format(($peni_delay * $cur_rest * $peni_percent), 2, '.', '');
    $peni_amount = $peni_delay * $peni_in_contract;

    $invoice_number = Q("SELECT * FROM `#_mdd_invoice` WHERE `invoice_number` = '$invoice' AND `status` != 0", array())->row();

    intval($peni) === 0 ? $status = 0 : $status = 1;
					// записываем пени, так как она уже точно начисляется и все данные есть	
    O('_mdd_peni')->create(array(
      'contract_id' => $id,
      'contract_number' => $_POST['renter_document'],
      'renter' => $renter_name,
      'month' => $invoice_month,
      'year' => $invoice_year,
      'amount' => $peni_amount,
      'summa' => $cur_rest,
      'peni' => $peni,
      'rest' => $peni,
      'delay' => $peni_delay,
      'peni_invoice' => $invoice,
      'ground' => 2,
      'status' => $status
    ));	

				// апдейтим баланс договора
    $balance_now = Q("SELECT `balance` FROM `#_mdd_contracts` WHERE `id` = $id", array())->row('balance');
    $balance_ren_now = Q("SELECT `balance` FROM `#_mdd_renters` WHERE `id` = ?i", array($_POST['renter_id']))->row('balance');

    $new_balance = $balance_now - $peni;
    $new_balance_ren = $balance_ren_now - $peni;

    $sql_balance_peni = "UPDATE `db_mdd_renters` SET `balance` = '$new_balance_ren' WHERE `db_mdd_renters`.`id` = '$renter_id'";
    $sql_cont_balance_peni = "UPDATE `db_mdd_contracts` SET `balance` = '$new_balance' WHERE `db_mdd_contracts`.`id` = '$id'";

    $conn->query($sql_balance_peni);
    $conn->query($sql_cont_balance_peni);

    $contract_number = Q("SELECT `number`, `balance` FROM `#_mdd_contracts` WHERE `id` = $id", array())->row();

				//  записываем в тааблицу балансов
    O('_mdd_balance')->create(array(
      'renter_id' => $_POST['renter_id'],
      'contract_id' => $id,
      'balance' => $contract_number['balance'],
      'ground' => 'peni',
      'contract' => $contract_number['number'],
      'date' => $invoice_date_balance,
      'renter' => $renter_full_name,
      'ground_id' => $invoice,
      'summa' => $peni,
    ));

    if ($period_sum == 0) {
      break;
    }

					// если сумма больше остатка по счету, то...
    if ($period_sum >= $invoice_number['rest']) {
			
						// записываем в остаток 0, и меняем статус
      $sql = "UPDATE `db_mdd_invoice` SET `rest` = 0, `status` = 0 WHERE `db_mdd_invoice`.`invoice_number` = $invoice";

      $conn->query($sql);

						// пересчитываем остаточную сумму
      $period_sum -= intval($invoice_number['rest']);
					//если сумма меньше остатка по счету
    } else {
						// высчитываем остаток и перезаписываем
      $rest = intval($invoice_number['rest']) - $period_sum;
      $sql = "UPDATE `db_mdd_invoice` SET `rest` = '$rest' WHERE `db_mdd_invoice`.`invoice_number` = '$invoice'";

      $conn->query($sql);
    }

  }
} 
		 
		// изменяем массив счетов на строку чтобы записать в БД
$invoices = implode(",", $_POST['invoices']);

		// записываем оплату
if (isset($_POST['summa']) && isset($_POST['date']) && isset($_POST['renter_name']) && isset($_POST['renter_document']) && isset($_POST['number'])) {
  O('_mdd_payments')->create(array(
    'renter_name' => $_POST['renter_name'],
    'date' => $_POST['date'],
    'summa' => to_money($_POST['summa']),
    'document' => $_POST['number'],
    'payment_info' => $_POST['renter_document'],
    'invoices' => $invoices,
  ));
}
		
		// берем общий баланс арендодателя и баланс контракта	
$balance = Q("SELECT `balance` FROM `#_mdd_renters` WHERE `id` = ?i", array($renter_id))->row('balance');
$contract_balance = Q("SELECT `balance` FROM `#_mdd_contracts` WHERE `id` = ?i", array($id))->row('balance');
		// прибавляем им сумму
$balance += $_POST['summa'];
$contract_balance += $_POST['summa'];

$sql_balance = "UPDATE `db_mdd_renters` SET `balance` = $balance WHERE `db_mdd_renters`.`id` = $renter_id";
$sql_cont_balance = "UPDATE `db_mdd_contracts` SET `balance` = $contract_balance WHERE `db_mdd_contracts`.`id` = $id";

$conn->query($sql_balance);
$conn->query($sql_cont_balance);

		// обновляем баланс при оплате
$renter_full_name = Q("SELECT `full_name` FROM `#_mdd_renters` WHERE `id` = ?i", array($_POST['renter_id']))->row('full_name');
$balance_in_contract = Q("SELECT `ground` FROM `#_mdd_contracts` WHERE `id` = ?i", array($id))->row();

O('_mdd_balance')->create(array(
  'renter_id' => $_POST['renter_id'],
  'contract_id' => $id,
  'balance' => $contract_balance,
  'ground' => 'payment',
  'contract' => $contract_number['number'],
  'date' => $_POST['date'],
  'renter' => $renter_full_name,
  'ground_id' => $balance_in_contract['ground'],
  'summa' => to_money($rest_sum),
));

$conn->close();
