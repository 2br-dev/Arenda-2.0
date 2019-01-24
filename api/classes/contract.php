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
            `contract`.`start_arenda`, `contract`.`status`, 
            
            `room`.`id` as `room_id`, `room`.`number` as `room_number`, `room`.`floor`, `room`.`square`,
            `room`.`number_scheme`, 
            
            `renter`.`id` as `renter_id`, `renter`.`short_name`, `renter`.`full_name`, `renter`.`ogrn`, `renter`.`kpp`,
            `renter`.`inn`, `renter`.`bank_bik`, `renter`.`bank_ks`, `renter`.`bank_rs`, `renter`.`bank_name`,`renter`.`email`, 
            `renter`.`phone`, `renter`.`balance`, `renter`.`form`, `renter`.`post_adress`, `renter`.`uridich_address`  
            
            FROM `#_mdd_contracts` as `contract`					
            LEFT JOIN `#_mdd_rooms` as `room` ON `contract`.`rooms` = `room`.`id`
            LEFT JOIN `#_mdd_renters` as `renter` ON `contract`.`renter` = `renter`.`id` WHERE `status` = ?i", array($status))->all();

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
  
    function payWithDiscount($invoice, $renter_id, $id, $summa, $db){
        // берём скидку и сумму по текущему счёту
        $discount = Q("SELECT `discount` FROM `#_mdd_invoice` WHERE `invoice_number` = ?i", array($invoice))->row('discount');
        $cur_summa = Q("SELECT `summa`    FROM `#_mdd_invoice` WHERE `invoice_number` = ?i", array($invoice))->row('summa');
          
        // остаток разнице скидки с разницой суммы и оставшейся суммы после оплаты пени
        $rest = $discount - ($cur_summa - $summa);
        $updated_balance = $cur_summa - $discount;
  
        // берём баланс арендатора и баланс договора
        $balance = Q("SELECT `balance` FROM `#_mdd_renters` WHERE `id` = ?i", array($renter_id))->row('balance');
        $contract_balance = Q("SELECT `balance` FROM `#_mdd_contracts` WHERE `id` = ?i", array($id))->row('balance');
             
        // плюсуем разницу
        $balance += $updated_balance;
        $contract_balance += $updated_balance;
             
        // апдейтим для счёта сумму и остаток
        $upd_summa          = "UPDATE `db_mdd_invoice` SET `summa` = '$discount' WHERE `invoice_number` = '$invoice'";
        $upd_rest           = "UPDATE `db_mdd_invoice` SET `rest` = '$rest' WHERE `invoice_number` = '$invoice'";
        $db->query($upd_summa);
        $db->query($upd_rest);

        // запрашиваем изменения балансов
        $sql_balance        = "UPDATE `db_mdd_renters` SET `balance` = '$balance' WHERE `db_mdd_renters`.`id` = '$renter_id'";
        $sql_cont_balance   = "UPDATE `db_mdd_contracts` SET `balance` = '$contract_balance' WHERE `db_mdd_contracts`.`id` = '$id'";
        $db->query($sql_balance);
        $db->query($sql_cont_balance);
    }
}