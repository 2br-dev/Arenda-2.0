<?php

include_once '../../define.php';
include_once '../classes/invoice.php';

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

        // берём переменную так как она нам будет нужна
        $start = $start_arenda;

        // для сравнения
        $start_arenda = explode('.', $start_arenda); // [day, month, year] -> numbers
        
        // год и месяц по счёту
        $invoice_year = Q("SELECT `period_year` FROM `#_mdd_invoice` WHERE `id` = ?s", array($invoice))->row('period_year'); // 2019
        $invoice_month = Q("SELECT `period_month` FROM `#_mdd_invoice` WHERE `id` = ?s", array($invoice))->row('period_month'); // январь

        // если год оплаты совпадает с годом счёта и месяц оплаты совпадает с месяцем в счёта то пени не считаем
        if ($start_arenda[2] == $invoice_year && intval($start_arenda[1]) == intval($invoice_month)) 
        {
            $difference = round((strtotime($payment_date) - strtotime($start)) / 86400);       
        } 
            else //иначе собираем в строку дату счёта
        {
            $invoice_from = implode('.', array('01', $invoice_month, $invoice_year)); 
            $difference = round((strtotime($payment_date) - strtotime($invoice_from)) / 86400);
        }
        
        // если разница меньше нуля (счёт был оплачен раньше даты)
        if ($difference < 0) {
            $difference = 0; // то разница равня нулю
        }

        return $difference;
    }

    function record_peni_balance($renter_id, $id, $contract_balance, $renter_document, $invoice_date, $renter_full_name, $payed_peni, $summa) {
        O('_mdd_balance')->create(array(
            'renter_id' => $renter_id,
            'contract_id' => $id,
            'balance' => number_format(($contract_balance + $summa), 2, '.', ''),
            'ground' => 'peni-payment',
            'contract' => $renter_document,
            'date' => $invoice_date,
            'renter' => $renter_full_name,
            'ground_id' => $payed_peni['peni_invoice'],
            'summa' => number_format(($summa), 2, '.', ''),
        ));
    }
    
 /*    function countPeni($days_difference, $start_peni_day, $invoice, $peni_percent, $peni_in_contract, 
    $id, $renter_document, $renter_full_name, $invoice_month, $invoice_year) {
        $peni_delay = $days_difference - intval($start_peni_day);
        // берём сумму и коэффициент по текущему счёту
        $cur_summa = Q("SELECT `rest`    FROM `#_mdd_invoice` WHERE `id` = ?i ORDER BY `id` DESC", array($invoice))->row('rest');
        $cur_amount = Q("SELECT `amount`	FROM `#_mdd_invoice` WHERE `id` = ?i ORDER BY `id` DESC", array($invoice))->row('amount');

        // за этот месяц будет начислять пени и мы можем подсчитать
        $peni = number_format(($peni_delay * $cur_summa * $peni_percent), 2, '.', '');
        $peni_amount = $peni_delay * $peni_in_contract;

        $invoice_number = Q("SELECT * FROM `#_mdd_invoice` WHERE `id` = '$invoice' AND `status` != 0", array())->row();
    
        floatval($peni) == 0 ? $status = 0 : $status = 1;
        
        // записываем пени, так как она уже точно начисляется и все данные есть	
        if ($status == 1) {
            O('_mdd_peni')->create(array(
                'contract_id' => $id,
                'contract_number' => $renter_document,
                'renter' => $renter_full_name,
                'month' => $invoice_month,
                'year' => $invoice_year,
                'amount' => $peni_amount,
                'summa' => $cur_summa,
                'peni' => $peni,
                'rest' => $peni,
                'delay' => $peni_delay,
                'peni_invoice' => $invoice,
                'ground' => 2,
                'status' => $status
            ));
        }
    
        // апдейтим баланс договора
        $balance_now = Q("SELECT `balance` FROM `#_mdd_contracts` WHERE `id` = ?i", array($id))->row('balance');
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
            'balance' => $new_balance,
            'ground' => 'peni',
            'contract' => $renter_document,
            'date' => $invoice_date,
            'renter' => $renter_full_name,
            'ground_id' => $invoice,
            'summa' => $peni,
        ));
    } */

    function pay($payment_date, $date, $invoices, $renter_document, $summa, $renter_id, $id, $renter_full_name, $db, $peni_percent) {
        
        $payment_year = intval($date[0]);
        $payment_month = intval($date[1]);
        $payment_day = intval($date[2]);
        
        foreach($invoices as $invoice) {   

            if ($summa <= 0) {
                break;
            }

            // дата счёта
            $invoice_date = Q("SELECT `invoice_date` FROM `#_mdd_invoice` WHERE `id` = ?s", array($invoice))->row('invoice_date');

            // дата начала аренды по договору
            $start_arenda = Q("SELECT `start_arenda` FROM `#_mdd_contracts` WHERE `id` = ?i", array($id))->row('start_arenda');
            //первый месяц и год аренды по договору
            $start_arenda_dates = explode('.', $start_arenda);
            $start_arenda_month = intval($start_arenda_dates[1]);
            $start_arenda_year = intval($start_arenda_dates[2]);

            $modified = Q("SELECT `modified` FROm `#_mdd_invoice` WHERE `id` = ?s", array($invoice))->row('modified');

            // баланс в контракте 
            $contract_balance = floatval(Q("SELECT `balance` FROM `#_mdd_contracts` WHERE `id` = ?s", array($id))->row('balance'));
         /*  
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
                      $this->record_peni_balance($renter_id, $id, $contract_balance, $renter_document, $invoice_date, $renter_full_name, $payed_peni, $summa);

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
                      $this->record_peni_balance($renter_id, $id, $contract_balance, $renter_document, $invoice_date, $renter_full_name, $payed_peni, $summa);
          
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
                      $this->record_peni_balance($renter_id, $id, $contract_balance, $renter_document, $invoice_date, $renter_full_name, $payed_peni, $summa);
          
                      // отнимаем из суммы пени по текущему счёту
                      $summa -= $payed_peni['peni'];
                      // если сумма меньше нуля то приравниваем ее к нулю
                      $summa <= 0 ? $summa = 0 : null;         
                      break;
                    }
                } // foreach $allpeni
            } // if */
 
            // получаем месяц и год выставленного счёта
            $invoice_month = Q("SELECT `period_month` FROM `#_mdd_invoice` WHERE `id` = ?s ORDER BY `id` DESC", array($invoice))->row('period_month');
            $invoice_year = Q("SELECT `period_year` FROM `#_mdd_invoice` WHERE `id` = ?s ORDER BY `id` DESC", array($invoice))->row('period_year');
        
            // получаем день начисления пени и саму пени
            $start_peni_day = Q("SELECT `start_peni` FROM `#_mdd_contracts` WHERE `id` = ?i", array($id))->row('start_peni');
            $peni_in_contract = Q("SELECT `peni` FROM `#_mdd_contracts` WHERE `id` = ?i", array($id))->row('peni');
      
            // количество дней когда будет скидка 
            if ($start_arenda_month == $payment_month && $start_arenda_year == $payment_year) { // если первый месяц берём из переменной
                $discount_days = Q("SELECT `discount_days` FROM `#_mdd_contracts` WHERE `id` = ?s", array($id))->row('discount_days');
            } else { // иначе 5 дней
                $discount_days = 4;      
            }

            // находим разницу в днях, когда был оплачен счёт
            $days_difference = $this->countDayDifference($start_arenda, $payment_date, $invoice, $discount_days);  

            echo json_encode(array(
                'days_difference' => $days_difference,
                'discount_days' => $discount_days,
                'invoice_month' => $invoice_month,
                'payment_month' => $payment_month,
                'invoice_year' => $invoice_year,
                'payment_year' => $payment_year,
                'modif' => $modified,
                'summa' => $summa
            ));
            //проверяем дату когда был оплачен счёт на предмет соответсвия скидке
            if (
                intval($days_difference)  <=  intval($discount_days) && 
                intval($invoice_month)    >=  intval($payment_month) && 
                intval($invoice_year)     >=  intval($payment_year) && 
                intval($summa)            !=  0 &&
                intval($modified)         ==  0
            ) {            

                $Invoice = new Invoice($db);

                $start_arenda_month == $invoice_month ? $first = 1 : $first = 0;

                $Invoice->payWithDiscount($invoice, $renter_id, $id, $summa, $db, $first); // оплачиваем со скидкой
            }
           

            // проверяем дату оплаты на предмет начиления пени
            /* if ($days_difference > intval($start_peni_day) && intval($start_arenda_month) != intval($invoice_month)) {
                
                $peni_delay = $days_difference - intval($start_peni_day);
                // берём сумму и коэффициент по текущему счёту
                $cur_summa = Q("SELECT `rest`    FROM `#_mdd_invoice` WHERE `id` = ?i ORDER BY `id` DESC", array($invoice))->row('rest');
                $cur_amount = Q("SELECT `amount`	FROM `#_mdd_invoice` WHERE `id` = ?i ORDER BY `id` DESC", array($invoice))->row('amount');

                // за этот месяц будет начислять пени и мы можем подсчитать
                $peni = number_format(($peni_delay * $cur_summa * $peni_percent), 2, '.', '');
                $peni_amount = $peni_delay * $peni_in_contract;
   
                $invoice_number = Q("SELECT * FROM `#_mdd_invoice` WHERE `id` = '$invoice' AND `status` != 0", array())->row();
            
                floatval($peni) == 0 ? $status = 0 : $status = 1;
                
                // записываем пени, так как она уже точно начисляется и все данные есть	
                if ($status == 1) {
                    O('_mdd_peni')->create(array(
                        'contract_id' => $id,
                        'contract_number' => $renter_document,
                        'renter' => $renter_full_name,
                        'month' => $invoice_month,
                        'year' => $invoice_year,
                        'amount' => $peni_amount,
                        'summa' => $cur_summa,
                        'peni' => $peni,
                        'rest' => $peni,
                        'delay' => $peni_delay,
                        'peni_invoice' => $invoice,
                        'ground' => 2,
                        'status' => $status
                    ));
                }
            
                // апдейтим баланс договора
                $balance_now = Q("SELECT `balance` FROM `#_mdd_contracts` WHERE `id` = ?i", array($id))->row('balance');
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
                    'balance' => $new_balance,
                    'ground' => 'peni',
                    'contract' => $renter_document,
                    'date' => $invoice_date,
                    'renter' => $renter_full_name,
                    'ground_id' => $invoice,
                    'summa' => $peni,
                ));
            } */

            $invoice_rest = Q("SELECT `rest` FROM `#_mdd_invoice` WHERE `id` = ?i ORDER BY `id` DESC", array($invoice))->row('rest');

            // если сумма больше остатка по счету, то...
            if ($summa >= $invoice_rest) {
                // записываем в остаток 0, и меняем статус
                $sql = "UPDATE `db_mdd_invoice` SET `rest` = 0, `status` = 0 WHERE `db_mdd_invoice`.`id` = $invoice";
                $db->query($sql);
    
                //если сумма меньше остатка по счету
            } else {
                // высчитываем остаток и перезаписываем
                $rest = $invoice_rest - $summa;
                $sql = "UPDATE `db_mdd_invoice` SET `rest` = '$rest' WHERE `db_mdd_invoice`.`id` = '$invoice'";
        
                $db->query($sql);
            }        

            // пересчитываем остаточную сумму
            $summa -= $invoice_rest;

        } // foreach $invoices   
        
        return $summa;
    } 
}