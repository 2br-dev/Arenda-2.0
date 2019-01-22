<?php

include_once '../../define.php';

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
        $rooms = Q("SELECT * FROM `#_mdd_peni` 
          WHERE `renter` = ?s 
          ORDER BY `id` ASC", array($shortname))->all();
    
        return $rooms;
    }
  
}