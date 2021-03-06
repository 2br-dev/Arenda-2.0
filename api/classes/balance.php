<?php

include_once '../../define.php';

class Balance
{
    // database connection and table name
    private $conn;
    private $table_name = 'db_mdd_balance';
 
    // object properties
    public $id;
    public $summa;
    public $renter_id;
    public $contract_id;
    public $balance;
    public $ground;
    public $contract;
    public $date;
    public $renter;
    public $ground_id;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function read(){
        $balances = Q("SELECT 
        `table`.`balance`, `table`.`summa`, `table`.`id`, `table`.`renter`, 
        `table`.`ground_id`, `table`.`ground`, `table`.`contract`, `table`.`date`,
        
        `renters`.`short_name`,
        `contracts`.`start_arenda`

        FROM `#_mdd_balance` as `table`
        
        LEFT JOIN `#_mdd_renters` as `renters`
        ON `table`.`renter`=`renters`.`full_name`

        LEFT JOIN `#_mdd_contracts` as `contracts`
        ON `table`.`contract_id`=`contracts`.`id`

        ORDER BY `table`.`id` DESC")->all();
    
        return $balances;
    }

    function read_by_id($id){
        $balances = Q("SELECT 
        `table`.`balance`, `table`.`summa`, `table`.`id`, `table`.`renter`, 
        `table`.`ground_id`, `table`.`ground`, `table`.`contract`, `table`.`date`,
        
        `renters`.`short_name`,
        `contracts`.`start_arenda`,
        `invoices`.`rest`

        FROM `#_mdd_balance` as `table`
        
        LEFT JOIN `#_mdd_renters` as `renters`
        ON `table`.`renter`=`renters`.`full_name`

        LEFT JOIN `#_mdd_contracts` as `contracts`
        ON `table`.`contract_id`=`contracts`.`id`

        LEFT JOIN `#_mdd_invoice` as `invoices`
        ON `table`.`ground_id`=`invoices`.`invoice_number`

        WHERE `table`.`renter_id` = ?s
        ORDER BY `table`.`id` DESC", array($id))->all();
    
        return $balances;
    }
  
}