<?php
include_once '../../define.php';
include_once '../classes/contract.php';

class Peni
{
    // database connection and table name
    private $conn;
    private $table_name = 'db_mdd_peni';
 
    // object properties
    public $id;
    public $rest;
    public $status;
    public $ground;
    public $peni_invoice;
    public $contract_id;
    public $renter;
    public $month;
    public $year;
    public $amount;
    public $summa;
    public $peni;
    public $delay;
    public $visible;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function read_by_renter($shortname){
        $peni = Q("SELECT * FROM `#_mdd_peni` 
          WHERE `renter` = ?s 
          ORDER BY `id` ASC", array($shortname))->all();
    
        return $peni;
    }

    function get_info($contract_id){
        $peni = Q("SELECT * FROM `#_mdd_contracts` 
          WHERE `id` = ?i 
          ORDER BY `id` ASC", array($contract_id))->row();
    
        return $peni;
    }

    function countDayDifference($start_arenda, $payment_date, $invoice) {

        // приводим дату оплаты к треб. формату
        $payment_date = implode($payment_date, '/');

        // берём переменную так как она нам будет нужна
        $start = $start_arenda;

        // для сравнения
        $start_arenda = explode('.', $start_arenda); // [day, month, year] -> numbers
        
        // год и месяц по счёту
        $invoice_year = Q("SELECT `period_year` FROM `#_mdd_invoice` WHERE `invoice_number` = ?s", array($invoice))->row('period_year'); // 2019
        $invoice_month = Q("SELECT `period_month` FROM `#_mdd_invoice` WHERE `invoice_number` = ?s", array($invoice))->row('period_month'); // январь

        // если год оплаты совпадает с годом счёта и месяц оплаты совпадает с месяцем в счёта то пени не считаем
        if ($start_arenda[2] == $invoice_year && intval($start_arenda[1]) == intval($invoice_month)) 
        {
            $difference = round(abs(strtotime($payment_date) - strtotime($start))/86400);       
        } 
            else //иначе собираем в строку дату счёта
        {
            $invoice_from = implode('.', array('01', $invoice_month, $invoice_year)); 
            $difference = round(abs(strtotime($payment_date) - strtotime($invoice_from))/86400);
        }
        
        // если разница меньше нуля (счёт был оплачен раньше даты)
        if ($difference < 0) {
            $difference = 0; // то разница равня нулю
        }

        return $difference;
    }

    function record_peni_balance($renter_id, $id, $contract_balance, $renter_document, $invoice_date, $renter_full_name, $payed_peni) {
        O('_mdd_balance')->create(array(
            'renter_id' => $renter_id,
            'contract_id' => $id,
            'balance' => $contract_balance + $payed_peni['peni'],
            'ground' => 'peni-payment',
            'contract' => $renter_document,
            'date' => $invoice_date,
            'renter' => $renter_full_name,
            'ground_id' => $payed_peni['peni_invoice'],
            'summa' => $payed_peni['peni'],
        ));
    }
    

    function pay($payment_date, $invoices, $renter_document, $summa, $renter_id, $id, $renter_full_name, $payment_year, $payment_month, $payment_day, $db, $number_of_days, $peni_percent) {

        $payment_month = intval(getMonthString($payment_month));
        
        foreach($invoices as $invoice) {   
            // все неоплаченные пени
            $allpeni = Q("SELECT * FROM `#_mdd_peni` WHERE `status` = 1 ORDER BY `id` ASC")->all();

            // дата счёта
            $invoice_date = Q("SELECT `invoice_date` FROM `#_mdd_invoice` WHERE `invoice_number` = ?s", array($invoice))->row('invoice_date');

            // дата начала аренды по договору
            $start_arenda = Q("SELECT `start_arenda` FROM `#_mdd_contracts` WHERE `id` = ?i", array($id))->row('start_arenda');

            // баланс в контракте 
            $contract_balance = intval(Q("SELECT `balance` FROM `#_mdd_contracts` WHERE `id` = ?s", array($id))->row('balance'));
          
            // если есть неоплаченные пени, то оплатим сначала их
            if (count($allpeni) > 0) {
                foreach ($allpeni as $peni) {

                    // найдём разница между оплаченной сумма и остатком по пени
                    $peni_rest = $summa - $peni['rest'];

                    $peni_id = $peni['id'];

                    // получаем сумму пени и номер счёта текущей пени
                    $payed_peni = Q("SELECT `peni`, `peni_invoice` FROM `#_mdd_peni` WHERE `id` = ?s", array($peni['id']))->row();
        
                    // если разница равна нулю
                    if ($peni_rest == 0) {

                      // делаем запрос и обновляем таблица  
                      $upd_peni_rest = "UPDATE `db_mdd_peni` SET `rest` = 0, `status` = 0  WHERE `id` = '$peni_id'";
                      $db->query($upd_peni_rest);

                      // в таком случаем сумма будет равна нулю
                      $summa = 0;
          
                      // записываем всё в баланс (fn.ini.php)
                      $this->record_peni_balance($renter_id, $id, $contract_balance, $renter_document, $invoice_date, $renter_full_name, $payed_peni);

                      // отнимаем из суммы пени по текущему счёту
                      $summa -= $payed_peni['peni'];
                      // если сумма меньше нуля то приравниваем ее к нулю
                      $summa <= 0 ? $summa = 0 : null; 
                      break;
          
                    } elseif ($peni_rest > 0) {
                      
                      // делаем запрос и обновляем 
                      $upd_peni_rest = "UPDATE `db_mdd_peni` SET `rest` = 0, `status` = 0  WHERE `id` = '$peni_id'";
                      $db->query($upd_peni_rest);
                      
                      $summa = $peni_rest;
                      // записываем всё в баланс (fn.ini.php)
                      $this->record_peni_balance($renter_id, $id, $contract_balance, $renter_document, $invoice_date, $renter_full_name, $payed_peni);
          
                      // отнимаем из суммы пени по текущему счёту
                      $summa -= $payed_peni['peni'];
                      // если сумма меньше нуля то приравниваем ее к нулю
                      $summa <= 0 ? $summa = 0 : null;     
                      break;
          
                    } else {
                      // если меньше нуля, то на нужно поменять знак
                      $summa = -1 * $peni_rest;
                 
                      // делаем запрос и обновляем табллицу
                      $upd_peni_rest = "UPDATE `db_mdd_peni` SET `rest` = '$summa', `status` = 1  WHERE `id` = '$peni_id'";
                      $db->query($upd_peni_rest);

                      // записываем всё в баланс (fn.ini.php)
                      $this->record_peni_balance($renter_id, $id, $contract_balance, $renter_document, $invoice_date_balance, $renter_full_name, $payed_peni);
          
                      // отнимаем из суммы пени по текущему счёту
                      $summa -= $payed_peni['peni'];
                      // если сумма меньше нуля то приравниваем ее к нулю
                      $summa <= 0 ? $summa = 0 : null;         
                      break;
                    }
                } // foreach $allpeni
            } // if
 
            // получаем месяц и год выставленного счёта и остаток
            $invoice_month = Q("SELECT `period_month` FROM `#_mdd_invoice` WHERE `invoice_number` = ?s", array($invoice))->row('period_month');
            $invoice_month = intval(getMonthString($invoice_month));
            $invoice_year = Q("SELECT `period_year` FROM `#_mdd_invoice` WHERE `invoice_number` = ?s", array($invoice))->row('period_year');
            $invoice_rest = Q("SELECT `rest` FROM `#_mdd_invoice` WHERE `invoice_number` = ?i", array($invoice))->row('rest');
        
            // получаем день начисления пени и саму пени
            $start_peni_day = Q("SELECT `start_peni` FROM `#_mdd_contracts` WHERE `id` = ?i", array($id))->row('start_peni');
            $peni_in_contract = Q("SELECT `peni` FROM `#_mdd_contracts` WHERE `id` = ?i", array($id))->row('peni');
      
            // количество дней когда будет скидка 
            if ($invoice_month == $payment_month) { // если первый месяц берём из переменной
                $discount_days = Q("SELECT `discount_days` FROM `#_mdd_contracts` WHERE `id` = ?s", array($id))->row('discount_days');
            } else { // иначе 5 дней
                $discount_days = 5;
            }
            
            // находим разницу в днях, когда был оплачен счёт
            $days_difference = $this->countDayDifference($start_arenda, $payment_date, $invoice, $discount_days);
                    
            //проверяем дату когда был оплачен счёт на предмет соответсвия скидке
            if ($days_difference > $discount_days) {
                $Contract = new Contract($db);
                $Contract->payWithDiscount($invoice, $renter_id, $id, $summa, $db); // оплачиваем со скидкой
            }
           
            // проверяем дату оплаты на предмет начиления пени
            if ($days_difference > $start_peni_day && $invoice_month != $payment_month) {

                // берём сумму и коэффициент по текущему счёту
                $cur_summa = Q("SELECT `summa`    FROM `#_mdd_invoice` WHERE `invoice_number` = ?i", array($invoice))->row('summa');
                $cur_amount = Q("SELECT `amount`	FROM `#_mdd_invoice` WHERE `invoice_number` = ?i", array($invoice))->row('amount');
          
                // за этот месяц будет начислять пени и мы можем подсчитать
                $peni = number_format(($peni_delay * $summa * $peni_percent), 2, '.', '');
                $peni_amount = $peni_delay * $peni_in_contract;
   
                $invoice_number = Q("SELECT * FROM `#_mdd_invoice` WHERE `invoice_number` = '$invoice' AND `status` != 0", array())->row();
            
                intval($peni) == 0 ? $status = 0 : $status = 1;
                
                // записываем пени, так как она уже точно начисляется и все данные есть	
                if (intval($peni) != 0) {
                    O('_mdd_peni')->create(array(
                        'contract_id' => $id,
                        'contract_number' => $renter_document,
                        'renter' => $renter_full_name,
                        'month' => $invoice_month,
                        'year' => $invoice_year,
                        'amount' => $peni_amount,
                        'summa' => $summa,
                        'peni' => $peni,
                        'rest' => $peni,
                        'delay' => $peni_delay,
                        'peni_invoice' => $invoice,
                        'ground' => 2,
                        'status' => $status
                    ));
                }
            
                // апдейтим баланс договора
                $balance_now = Q("SELECT `balance` FROM `#_mdd_contracts` WHERE `id` = $id", array())->row('balance');
                $balance_ren_now = Q("SELECT `balance` FROM `#_mdd_renters` WHERE `id` = ?i", array($renter_id))->row('balance');

                $new_balance = $balance_now - $peni;
                $new_balance_ren = $balance_ren_now - $peni;
            
                $sql_balance_peni = "UPDATE `db_mdd_renters` SET `balance` = '$new_balance_ren' WHERE `db_mdd_renters`.`id` = '$renter_id'";
                $sql_cont_balance_peni = "UPDATE `db_mdd_contracts` SET `balance` = '$new_balance' WHERE `db_mdd_contracts`.`id` = '$id'";
            
                $db->query($sql_balance_peni);
                $db->query($sql_cont_balance_peni);
            
                
                //  записываем в таблицу балансов
                O('_mdd_balance')->create(array(
                    'renter_id' => $renter_id,
                    'contract_id' => $id,
                    'balance' => $contract_balance,
                    'ground' => 'peni',
                    'contract' => $renter_document,
                    'date' => $invoice_date,
                    'renter' => $renter_full_name,
                    'ground_id' => $invoice,
                    'summa' => $peni,
                ));
            }
              
            if ($summa == 0) {
                break;
            }
        
            // если сумма больше остатка по счету, то...
            if ($summa >= $invoice_rest) {
                // записываем в остаток 0, и меняем статус
                $sql = "UPDATE `db_mdd_invoice` SET `rest` = 0, `status` = 0 WHERE `db_mdd_invoice`.`invoice_number` = $invoice";
                $db->query($sql);
        
                // пересчитываем остаточную сумму
                $summa -= intval($invoice_rest);
                //если сумма меньше остатка по счету
            } else {
                // высчитываем остаток и перезаписываем
                $rest = intval($invoice_rest) - $summa;
                $sql = "UPDATE `db_mdd_invoice` SET `rest` = '$rest' WHERE `db_mdd_invoice`.`invoice_number` = '$invoice'";
        
                $db->query($sql);
            }        
        } // foreach $invoices         
    } 
}