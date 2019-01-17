<?php
class Room
{
    // database connection and table name
    private $conn;
    private $table_name = 'db_mdd_rooms';
 
    // object properties
    public $id;
    public $number;
    public $number_scheme;
    public $square;
    public $floor;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function read(){
        $query = "SELECT * FROM `db_mdd_rooms` WHERE `visible` = 1 ORDER BY `id` ASC";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
     
        // execute query
        $stmt->execute();
     
        return $stmt;
    }
  
}