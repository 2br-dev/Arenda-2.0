<?php
class Contract
{
    // database connection and table name
    private $conn;
    private $table_name = 'db_mdd_contracts';
 
    // object properties
    public $id;
    public $balance;
    public $start_peni;
    public $datetime;
    public $number;
    public $status;
    public $rooms;
    public $renter;
    public $summa;
    public $discoint;
    public $start_date;
    public $end_date;
    public $peni;
    public $start_arenda;
    public $ground;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function read(){
        $query = "SELECT * FROM `db_mdd_contracts` WHERE `visible` = 1 ORDER BY `id` ASC";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
     
        // execute query
        $stmt->execute();
     
        return $stmt;
    }
  
}