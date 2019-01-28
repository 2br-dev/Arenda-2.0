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
        $contract_summa =   Q("SELECT `summa` FROM `#_mdd_contracts` WHERE `id` = ?s", array($id))->row('summa');
        $invoice_summa  =   Q("SELECT `summa` FROM `#_mdd_invoice` WHERE `invoice_number` = ?i", array($invoice))->row('summa');

        if (intval($summa) < intval($discount) ) {
            $final_sum      =   intval($summa * ($discount / $contract_summa));        
            $difference     =   $invoice_summa - $final_sum;
        } else {
            $final_sum      =   $discount;
            $difference     =   $contract_summa - $final_sum;
        }

        // так же нам нужно обновить все остальные балансы по этому договору
        $balance_id = Q("SELECT `id` FROM `#_mdd_balance` WHERE `ground_id` = ?s", array($invoice))->row('id');
        $update_rest_balance = "UPDATE `db_mdd_balance` 
            SET `balance` = (`balance` + $difference) 
            WHERE `id` > '$balance_id'
            AND `contract_id` = '$id'";

        $db->query($update_rest_balance);    

        $upd_summa          =   "UPDATE `db_mdd_invoice`    SET `summa`     = '$final_sum'               WHERE `invoice_number` = '$invoice'";
        $upd_rest           =   "UPDATE `db_mdd_invoice`    SET `rest`      = '$final_sum'                WHERE `invoice_number` = '$invoice'";

        $sql_all_balance    =   "UPDATE `db_mdd_balance`    SET `summa`     = '$final_sum'               WHERE `ground_id` = '$invoice'"; 
        $sql_rest_balance   =   "UPDATE `db_mdd_balance`    SET `balance`   = (`balance` + '$difference')  WHERE `ground_id` = '$invoice'";  

        $sql_balance        =   "UPDATE `db_mdd_renters`    SET `balance`   = (`balance` + '$difference')  WHERE `db_mdd_renters`.`id` = '$renter_id'";
        $sql_cont_balance   =   "UPDATE `db_mdd_contracts`  SET `balance`   = (`balance` + '$difference')  WHERE `db_mdd_contracts`.`id` = '$id'";

        $db->query($upd_summa);
        $db->query($upd_rest);
        $db->query($sql_balance);
        $db->query($sql_cont_balance); 
        $db->query($sql_all_balance);
        $db->query($sql_rest_balance);

        return $final_sum;
    }
}