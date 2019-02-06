<?php

include_once '../config/database.php';
include_once '../classes/peni.php';

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
    public function __construct($db)
    {
        $this->conn = $db;
    }

    function read_all()
    {
        $invoices = Q("SELECT * FROM `#_mdd_invoice` ORDER BY `id` DESC", array())->all();

        return $invoices;
    }


    function read_renter_year($renter, $year)
    {
        $invoices = Q("SELECT * FROM `#_mdd_invoice` 
            WHERE `renter_id` = ?s 
            AND `period_year` = ?s
            ORDER BY `id` DESC", array($renter, $year))->all();

        return $invoices;
    }

    function read_renter_year_month($renter, $year, $month)
    {
        $invoices = Q("SELECT * FROM `#_mdd_invoice` 
            WHERE `renter_id` = ?s 
            AND `period_year` = ?s
            AND `period_month` = ?s
            ORDER BY `id` DESC", array($renter, $year, $month))->all();

        return $invoices;
    }

    function read_year($year)
    {
        $invoices = Q("SELECT * FROM `#_mdd_invoice` 
            WHERE `period_year` = ?s
            ORDER BY `id` DESC", array($year))->all();

        return $invoices;
    }

    function read_year_month($year, $month)
    {
        $invoices = Q("SELECT * FROM `#_mdd_invoice` 
            WHERE `period_year` = ?s 
            AND `period_month` = ?s
            ORDER BY `id` DESC", array($year, $month))->all();

        return $invoices;
    }

    function read_by_contract($id)
    {
        $invoices = Q("SELECT * FROM `#_mdd_invoice` 
            WHERE `contract_id` = ?s 
            ORDER BY `id` DESC", array($id))->all();

        return $invoices;
    }

    function read_active($id)
    {
        $invoices = Q("SELECT `id` FROM `#_mdd_invoice` 
            WHERE `contract_id` = ?s AND `rest` != ?i
            ORDER BY `id` ASC", array($id, 0))->all();

        return $invoices;
    }

    function read_by_renter_fullname($fullname)
    {
        $invoices = Q("SELECT `i`.`summa` as `summa`, `c`.`summa` as `contract_summa`, `i`.`invoice_number`, `i`.`period_month`,
            `i`.`period_year`, `i`.`modified`, `c`.`start_arenda`, `i`.`invoice_date`, `c`.`discount_days`, `i`.`discount`, `i`.`status`
             FROM `#_mdd_invoice` AS `i`
            
            LEFT JOIN `#_mdd_contracts` AS `c` ON `i`.`contract_id` = `c`.`id`
            
            WHERE `i`.`renter` = ?s 
            ORDER BY `i`.`id` DESC", array($fullname))->all();

        return $invoices;
    }

    function payWithDiscount($invoice, $renter_id, $id, $summa, $db, $first)
    {
        // берём скидку и сумму по текущему счёту
        $discount = floatval(Q("SELECT `discount` FROM `#_mdd_invoice` WHERE `id` = ?i", array($invoice))->row('discount'));
        $contract_summa = floatval(Q("SELECT `summa` FROM `#_mdd_contracts` WHERE `id` = ?s", array($id))->row('summa'));
        $invoice_summa = floatval(Q("SELECT `summa` FROM `#_mdd_invoice` WHERE `id` = ?i", array($invoice))->row('summa'));
        $invoice_rest = floatval(Q("SELECT `rest` FROM `#_mdd_invoice` WHERE `id` = ?i", array($invoice))->row('rest'));

        if ($first == 1) {
            $final_sum = floatval($invoice_summa * ($discount / $contract_summa));
            $difference = floatval($invoice_summa) - floatval($final_sum);
        } else {
            $final_sum = $discount;
            $difference = floatval($contract_summa) - floatval($final_sum);
        }
        if ($summa >= $invoice_rest - ($invoice_summa - $discount)) {
            
            // так же нам нужно обновить все остальные балансы по этому договору
            $ground = Q("SELECT `invoice_number` FROM `#_mdd_invoice` WHERE `id` = ?i", array($invoice))->row('invoice_number');

            $balance_id = Q("SELECT `id` FROM `#_mdd_balance` WHERE `ground_id` = ?s", array($ground))->row('id');

            $update_rest_balance = "UPDATE `db_mdd_balance` 
            SET `balance` = (`balance` + '$difference') 
            WHERE `id` >= '$balance_id'
            AND `contract_id` = '$id'";

            $db->query($update_rest_balance);

            $upd_summa = "UPDATE `db_mdd_invoice`    SET `summa`     = '$final_sum'   WHERE `id` = '$invoice'";
            $upd_rest = "UPDATE `db_mdd_invoice`    SET `rest`      = '$final_sum'   WHERE `id` = '$invoice'";

            $sql_all_balance = "UPDATE `db_mdd_balance`    SET `summa`     = '$final_sum'   WHERE `ground_id` = '$ground'";
            $sql_rest_balance = "UPDATE `db_mdd_balance`    SET `balance`   = (`balance` + '$difference')  WHERE `ground_id` = '$ground'";

            $sql_balance = "UPDATE `db_mdd_renters`    SET `balance`   = (`balance` + '$difference')  WHERE `db_mdd_renters`.`id` = '$renter_id'";
            $sql_cont_balance = "UPDATE `db_mdd_contracts`  SET `balance`   = (`balance` + '$difference')  WHERE `db_mdd_contracts`.`id` = '$id'";

            $db->query($upd_summa);
            $db->query($upd_rest);
            $db->query($sql_balance);
            $db->query($sql_cont_balance);
            $db->query($sql_all_balance);
        /* $db->query($sql_rest_balance); */
        }


        return $summa - $final_sum;
    }

    function countDaysDifference($id, $contract_id, $renter_id, $payment_date)
    {
        $database = new Database();
        $db = $database->getConnection();
        $Peni = new Peni($db);

        $start_arenda = Q("SELECT `start_arenda` FROM `#_mdd_contracts` WHERE `id` = ?i", array($contract_id))->row('start_arenda');
        $invoice_month = Q("SELECT `period_month` FROM `#_mdd_invoice` WHERE `id` = ?i", array($id))->row('period_month');
        $invoice_year = Q("SELECT `period_year` FROM `#_mdd_invoice` WHERE `id` = ?i", array($id))->row('period_year');
        $payment_array = explode('-', $payment_date);

        if (intval(getMonthString($invoice_month)) == intval($payment_array[1]) && intval($invoice_year) == intval($payment_array[0])) { 
            # если первый месяц берём из переменной
            $discount_days = Q("SELECT `discount_days` FROM `#_mdd_contracts` WHERE `id` = ?s", array($contract_id))->row('discount_days');
        } else { // иначе 5 дней
            $discount_days = 4;
        }

        $days_difference = $Peni->countDayDifference($start_arenda, $payment_date, $contract_id, $discount_days);
        return $days_difference;
    }

}