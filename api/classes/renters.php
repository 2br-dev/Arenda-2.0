<?php
include_once '../../define.php';

class Renters
{
    // database connection and table name
    private $conn;
    private $table_name = "db_mdd_renters";
 
    // object properties
    public $id;
    public $balance;
    public $full_name;
    public $short_name;
    public $form;
    public $inn;
    public $kpp;
    public $ogrn;
    public $uridich_address;
    public $post_adress;
    public $phone;
    public $email;
    public $bank_name;
    public $bank_rs;
    public $bank_ks;
    public $bank_bik;
    public $chief_position;
    public $chief_name;
    public $chief_document;
    public $login;
    public $visible;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function read_one($id){
        $renters = Q("SELECT * FROM `#_mdd_renters` WHERE `id` = ?s", array($id))->all();
        return $renters;
    }

    function read_all(){
        $renters = Q("SELECT * FROM `#_mdd_renters` WHERE `visible` = ?i", array(1))->all();
        return $renters;
    }

}