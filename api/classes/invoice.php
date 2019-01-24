<?php

include_once '../config/database.php';

class Invoice
{
    // database connection and table name
    private $conn;
    private $table_name = 'db_mdd_invoice';
 
    // object properties
    public $id;
    public $discount;
    public $invoice_date;
    public $invoice_number;
    public $renter;
    public $contract_id;
    public $period_year;
    public $period_month;
    public $status;
    public $summa;
    public $rest;
    public $amount;
    public $schet_id;
    public $akt_date;
    public $akt_number;
    public $akt_id;
    public $sf_date;
    public $sf_number;


    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function read_all() {   
      $invoices = Q("SELECT * FROM `#_mdd_invoice` ORDER BY `id` DESC", array())->all();

      return $invoices;
    }


    function read_renter_year($renter, $year) {   
        $invoices = Q("SELECT * FROM `#_mdd_invoice` 
            WHERE `renter` = ?s 
            AND `period_year` = ?s
            ORDER BY `id` DESC", array($renter, $year))->all();
  
        return $invoices;
    }

    function read_renter_year_month($renter, $year, $month) {   
        $invoices = Q("SELECT * FROM `#_mdd_invoice` 
            WHERE `renter` = ?s 
            AND `period_year` = ?s
            AND `period_month` = ?s
            ORDER BY `id` DESC", array($renter, $year, $month))->all();
  
        return $invoices;
    }

    function read_year($year) {   
        $invoices = Q("SELECT * FROM `#_mdd_invoice` 
            WHERE `period_year` = ?s
            ORDER BY `id` DESC", array($year))->all();
  
        return $invoices;
    }
     
    function read_year_month($year, $month) {   
        $invoices = Q("SELECT * FROM `#_mdd_invoice` 
            WHERE `period_year` = ?s 
            AND `period_month` = ?s
            ORDER BY `id` DESC", array($year, $month))->all();
  
        return $invoices;
    }  

    function read_by_contract($id) {   
        $invoices = Q("SELECT * FROM `#_mdd_invoice` 
            WHERE `contract_id` = ?s 
            ORDER BY `id` DESC", array($id))->all();
  
        return $invoices;
    }  

    function read_by_renter_fullname($fullname) {   
        $invoices = Q("SELECT * FROM `#_mdd_invoice` 
            WHERE `renter` = ?s 
            ORDER BY `id` DESC", array($fullname))->all();
  
        return $invoices;
    }  
    
    function payWithDiscount($invoice, $renter_id, $id, $summa, $db){
        // берём скидку и сумму по текущему счёту
        $discount           =   Q("SELECT `discount` FROM `#_mdd_invoice` WHERE `invoice_number` = ?i", array($invoice))->row('discount');
        $cur_summa          =   Q("SELECT `summa`    FROM `#_mdd_invoice` WHERE `invoice_number` = ?i", array($invoice))->row('summa');
          
        if (intval($cur_summa) < intval($discount)) {

            // ֎ внимание - хитрые вычисления ֎
            // в таком случае нам нужно произведение $текущей суммы по счёту и отношения $скидки и $суммы по договору 
            $contract_summa =   Q("SELECT `summa` FROM `#_mdd_contracts` WHERE `id` = ?s", array($id))->row('summa');
            $final_sum      =   intval($cur_summa * ($discount / $contract_summa));
        } else {
            $final_sum      =   $discount;
        }
  
        // берём баланс арендатора и баланс договора
        $balance            =   Q("SELECT `balance` FROM `#_mdd_renters` WHERE `id` = ?i", array($renter_id))->row('balance');
        $contract_balance   =   Q("SELECT `balance` FROM `#_mdd_contracts` WHERE `id` = ?i", array($id))->row('balance');
      
        $balance            =   $balance - $balance - $final_sum;
        $contract_balance   =   $contract_balance - $contract_balance  - $final_sum;

        $upd_summa          =   "UPDATE `db_mdd_invoice` SET `summa` = '$final_sum' WHERE `invoice_number` = '$invoice'";
        $upd_rest           =   "UPDATE `db_mdd_invoice` SET `rest` = '$final_sum' WHERE `invoice_number` = '$invoice'";
        $sql_balance        =   "UPDATE `db_mdd_renters` SET `balance` = '$balance' WHERE `db_mdd_renters`.`id` = '$renter_id'";
        $sql_cont_balance   =   "UPDATE `db_mdd_contracts` SET `balance` = '$contract_balance' WHERE `db_mdd_contracts`.`id` = '$id'";
        $sql_all_balance    =   "UPDATE `db_mdd_balance` SET  `summa` = '$final_sum' WHERE `ground_id` = '$invoice'";
        $sql_rest_balance   =   "UPDATE `db_mdd_balance` SET  `balance` = '$contract_balance' WHERE `ground_id` = '$invoice'";

        $db->query($upd_summa);
        $db->query($upd_rest);
        $db->query($sql_balance);
        $db->query($sql_cont_balance); 
        $db->query($sql_all_balance);
        $db->query($sql_rest_balance);

        return $final_sum;
    }
}