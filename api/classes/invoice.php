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
 
}