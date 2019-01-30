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

    function read($status){
        $reestr = Q("SELECT 
            `contract`.`id` as `contract_id`, `contract`.`number` as `contract_number`, `contract`.`datetime`,
            `contract`.`status`, `contract`.`summa`, `contract`.`start_date`, `contract`.`end_date`, `contract`.`peni`,
            `contract`.`start_arenda`, `contract`.`status`, `contract`.`discoint`, 
            
            `room`.`id` as `room_id`, `room`.`number` as `room_number`, `room`.`floor`, `room`.`square`,
            `room`.`number_scheme`, 
            
            `renter`.`id` as `renter_id`, `renter`.`short_name`, `renter`.`full_name`, `renter`.`ogrn`, `renter`.`kpp`,
            `renter`.`inn`, `renter`.`bank_bik`, `renter`.`bank_ks`, `renter`.`bank_rs`, `renter`.`bank_name`,`renter`.`email`, 
            `renter`.`phone`, `renter`.`balance`, `renter`.`form`, `renter`.`post_adress`, `renter`.`uridich_address`  
            
            FROM `#_mdd_contracts` as `contract`					
            LEFT JOIN `#_mdd_rooms` as `room` ON `contract`.`rooms` = `room`.`id`
            LEFT JOIN `#_mdd_renters` as `renter` ON `contract`.`renter` = `renter`.`id` WHERE `status` = ?i OR `status` = '0.5'", array($status))->all();

        return $reestr;    
    }

    function read_all(){
        $reestr = Q("SELECT 
            `contract`.`id` as `contract_id`, `contract`.`number` as `contract_number`, `contract`.`datetime`,
            `contract`.`status`, `contract`.`summa`, `contract`.`start_date`, `contract`.`end_date`, `contract`.`peni`,
            `contract`.`start_arenda`, `contract`.`status`, 
            
            `room`.`id` as `room_id`, `room`.`number` as `room_number`, `room`.`floor`, `room`.`square`,
            `room`.`number_scheme`, 
            
            `renter`.`id` as `renter_id`, `renter`.`short_name`, `renter`.`full_name`, `renter`.`ogrn`, `renter`.`kpp`,
            `renter`.`inn`, `renter`.`bank_bik`, `renter`.`bank_ks`, `renter`.`bank_rs`, `renter`.`bank_name`,`renter`.`email`, 
            `renter`.`phone`, `renter`.`balance`, `renter`.`form`, `renter`.`post_adress`, `renter`.`uridich_address`  
            
            FROM `#_mdd_contracts` as `contract`					
            LEFT JOIN `#_mdd_rooms` as `room` ON `contract`.`rooms` = `room`.`id`
            LEFT JOIN `#_mdd_renters` as `renter` ON `contract`.`renter` = `renter`.`id`")->all();
        
        return $reestr; 
    }

    function read_one($id){
        $contracts = Q("SELECT * FROM `#_mdd_contracts` WHERE `renter` = ?s",array($id))->all();
        
        return $contracts; 
    }

    function read_active() {
        $contracts = Q("SELECT `end_date`, `id` FROM `#_mdd_contracts`")->all();
        
        return $contracts; 
    }
  
}