<?php

declare (strict_types = 1);

namespace Fastest\Core\Modules;

final class printformsModule extends \Fastest\Core\Modules\Module
{
    public function router()
    {
        return $this->blockMethod();
    }

    public function blockMethod()
    {
        if ($_GET['ind'] == 'as') {
            $ind = $_GET['ind'];
            $print = 0;
            $month = 0;
            $date = 0;
            $disc = 0;
            $discount_summ = 0;
            $allpeni = 0;
        } else {
            $id = $_GET['num']; // id Документа из ГЕТ запроса
            $ind = $_GET['ind']; // Идентефикатор документа из ГЕТ запроса: sch - счет, akt - акт, sf - счет-фактура		
            $disc = $_GET['disc']; // Со скидкой или без - из ГЕТ запроса: disc = 0 - без скидки, disc = 1 - со скидкой
            $discount_summ = 0;
        }

        $pr = $_GET['pr']; // С печатью или без - из ГЕТ запроса: pr = 0 - без печати, pr = 1 - с печатью

        if (isset($_GET['peni'])) {
            $peni = $_GET['peni'];
            $allpeni = Q("SELECT * FROM `#_mdd_peni` as `peni`
			
					LEFT JOIN `#_mdd_grounds` as `ground`
					ON `peni`.`ground` = `ground`.`id`

					WHERE `peni` != ?i AND `peni`.`peni_invoice` = ?i ORDER BY `peni_invoice` DESC", array(0, $id))->row();

            $akt = Q("SELECT * FROM `#_mdd_invoice` WHERE `schet_id` = ?i", array($allpeni['peni_invoice']))->row();

            $allpeni['akt_id'] = $akt['invoice_number'];
            $allpeni['sf_id'] = $akt['invoice_number'];
            $allpeni['schet_id'] = $akt['invoice_number'];
            $allpeni['sf_number'] = $akt['invoice_number'];

            if ($peni == 1) {
                switch ($allpeni['month']) {
                    case '1':
                        $allpeni['month'] = 'Января';
                        break;
                    case '2':
                        $allpeni['month'] = 'Февраля';
                        break;
                    case '3':
                        $allpeni['month'] = 'Марта';
                        break;
                    case '4':
                        $allpeni['month'] = 'Апреля';
                        break;
                    case '5':
                        $allpeni['month'] = 'Мая';
                        break;
                    case '6':
                        $allpeni['month'] = 'Июня';
                        break;
                    case '7':
                        $allpeni['month'] = 'Июля';
                        break;
                    case '8':
                        $allpeni['month'] = 'Августа';
                        break;
                    case '9':
                        $allpeni['month'] = 'Сентября';
                        break;
                    case '10':
                        $allpeni['month'] = 'Октября';
                        break;
                    case '11':
                        $allpeni['month'] = 'Ноября';
                        break;
                    case '12':
                        $allpeni['month'] = 'Декабря';
                        break;
                }
            }
        } else $peni = 0;
				
				// Если Документ - Счет
        if ($ind == 'sch') {
            $print = Q("SELECT 

													`invoice`.`period_year`, 
													`invoice`.`period_month`, 
													`invoice`.`invoice_date`,
													`invoice`.`amount` as `invoice_amount`,
													`invoice`.`summa` as `invoice_summa`,
													`invoice`.`invoice_number` as `document_number`,

													`contract`.`id` as `contract_id`, 
													`contract`.`number` as `contract_number`,
													`contract`.`datetime` as `contract_date`,
													`contract`.`summa` as `contract_summa`, 
													`contract`.`ground` as `contract_ground`, 
													`contract`.`discoint`,

													`ground`.`id` as `ground_id`, 
													`ground`.`desc` as `ground_desc`, 
													`ground`.`price` as `ground_price`,
													`ground`.`ed` as `ground_ed`, 
													`ground`.`code_ed` as `ground_code`,

													`renter`.`id` as `renter_id`, 
													`renter`.`full_name` as `renter_name`, 
													`renter`.`short_name`,
													`renter`.`uridich_address` as `renter_address`, 
													`renter`.`inn`, `renter`.`kpp`,

													`room`.`id` as `room_id`, 
													`room`.`number` as `room_number`

							FROM `#_mdd_invoice` as `invoice`

													LEFT JOIN `#_mdd_contracts` as `contract`
							ON `invoice`.`contract_id` = `contract`.`id`

							LEFT JOIN `#_mdd_grounds` as `ground`
							ON `contract`.`ground` = `ground`.`id`

							LEFT JOIN `#_mdd_renters` as `renter`
							ON `contract`.`renter` = `renter`.`id`

							LEFT JOIN `#_mdd_rooms` as `room`
							ON `contract`.`rooms` = `room`.`id`

													WHERE `invoice`.`invoice_number` = ?i", array($id))->row();


            $discount_summ = $print['contract_summa'] - $print['discoint'];
            $discount_summ = number_format($discount_summ, 2, '.', '');

            switch ($print['period_month']) {
                case '01':
                    $print['print_date'] = "2018-01-31";
                    break;
                case '02':
                    $print['print_date'] = "2018-02-28";
                    break;
                case '03':
                    $print['print_date'] = "2018-03-31";
                    break;
                case '04':
                    $print['print_date'] = "2018-04-30";
                    break;
                case '05':
                    $print['print_date'] = "2018-05-31";
                    break;
                case '06':
                    $print['print_date'] = "2018-06-30";
                    break;
                case '07':
                    $print['print_date'] = "2018-07-31";
                    break;
                case '08':
                    $print['print_date'] = "2018-08-31";
                    break;
                case '09':
                    $print['print_date'] = "2018-09-30";
                    break;
                case '10':
                    $print['print_date'] = "2018-10-31";
                    break;
                case '11':
                    $print['print_date'] = "2018-11-30";
                    break;
                case '12':
                    $print['print_date'] = "2018-12-31";
                    break;

                default:
                    $print['print_date'] = "0000-00-00";
                    break;
            }
        }

				// Если Документ - Акт выполненных работ
        elseif ($ind == 'akt') {
            $print = Q("SELECT 

													`invoice`.`period_year`, 
													`invoice`.`period_month`, 
													`invoice`.`amount` as `invoice_amount`,
													`invoice`.`summa` as `invoice_summa`,
													`invoice`.`invoice_number` as `document_number`,

													`contract`.`id` as `contract_id`, 
													`contract`.`number` as `contract_number`,
													`contract`.`datetime` as `contract_date`,
													`contract`.`summa` as `contract_summa`, 
													`contract`.`ground` as `contract_ground`, 
													`contract`.`discoint`,

													`ground`.`id` as `ground_id`, 
													`ground`.`desc` as `ground_desc`, 
													`ground`.`price` as `ground_price`,
													`ground`.`ed` as `ground_ed`, 
													`ground`.`code_ed` as `ground_code`,

													`renter`.`id` as `renter_id`, 
													`renter`.`full_name` as `renter_name`, 
													`renter`.`short_name`,
													`renter`.`uridich_address` as `renter_address`, 
													`renter`.`inn`, `renter`.`kpp`,

													`room`.`id` as `room_id`, 
													`room`.`number` as `room_number`

							FROM `#_mdd_invoice` as `invoice`

													LEFT JOIN `#_mdd_contracts` as `contract`
							ON `invoice`.`contract_id` = `contract`.`id`

							LEFT JOIN `#_mdd_grounds` as `ground`
							ON `contract`.`ground` = `ground`.`id`

							LEFT JOIN `#_mdd_renters` as `renter`
							ON `contract`.`renter` = `renter`.`id`

							LEFT JOIN `#_mdd_rooms` as `room`
							ON `contract`.`rooms` = `room`.`id`

													WHERE `invoice`.`invoice_number` = ?i", array($id))->row();


            $discount_summ = $print['contract_summa'] - $print['discoint'];
            $discount_summ = number_format($discount_summ, 2, '.', '');

            switch ($print['period_month']) {
                case '01':
                    $print['print_date'] = "2018-01-31";
                    break;
                case '02':
                    $print['print_date'] = "2018-02-28";
                    break;
                case '03':
                    $print['print_date'] = "2018-03-31";
                    break;
                case '04':
                    $print['print_date'] = "2018-04-30";
                    break;
                case '05':
                    $print['print_date'] = "2018-05-31";
                    break;
                case '06':
                    $print['print_date'] = "2018-06-30";
                    break;
                case '07':
                    $print['print_date'] = "2018-07-31";
                    break;
                case '08':
                    $print['print_date'] = "2018-08-31";
                    break;
                case '09':
                    $print['print_date'] = "2018-09-30";
                    break;
                case '10':
                    $print['print_date'] = "2018-10-31";
                    break;
                case '11':
                    $print['print_date'] = "2018-11-30";
                    break;
                case '12':
                    $print['print_date'] = "2018-12-31";
                    break;

                default:
                    $print['print_date'] = "0000-00-00";
                    break;
            }


        }

				// Если Документы счет-фактура
        elseif ($ind == 'sf') {
            $print = Q("SELECT 

													`invoice`.`period_year`, 
													`invoice`.`period_month`, 
													`invoice`.`amount` as `invoice_amount`,
													`invoice`.`summa` as `invoice_summa`,
													`invoice`.`invoice_number` as `document_number`,

													`contract`.`id` as `contract_id`, 
													`contract`.`number` as `contract_number`,
													`contract`.`datetime` as `contract_date`,
													`contract`.`summa` as `contract_summa`, 
													`contract`.`ground` as `contract_ground`, 
													`contract`.`discoint`,

													`ground`.`id` as `ground_id`, 
													`ground`.`desc` as `ground_desc`, 
													`ground`.`price` as `ground_price`,
													`ground`.`ed` as `ground_ed`, 
													`ground`.`code_ed` as `ground_code`,

													`renter`.`id` as `renter_id`, 
													`renter`.`full_name` as `renter_name`, 
													`renter`.`short_name`,
													`renter`.`uridich_address` as `renter_address`, 
													`renter`.`inn`, `renter`.`kpp`,

													`room`.`id` as `room_id`, 
													`room`.`number` as `room_number`

							FROM `#_mdd_invoice` as `invoice`

													LEFT JOIN `#_mdd_contracts` as `contract`
							ON `invoice`.`contract_id` = `contract`.`id`

							LEFT JOIN `#_mdd_grounds` as `ground`
							ON `contract`.`ground` = `ground`.`id`

							LEFT JOIN `#_mdd_renters` as `renter`
							ON `contract`.`renter` = `renter`.`id`

							LEFT JOIN `#_mdd_rooms` as `room`
							ON `contract`.`rooms` = `room`.`id`

													WHERE `invoice`.`invoice_number` = ?i", array($id))->row();

            $discount_summ = $print['contract_summa'] - $print['discoint'];
            $discount_summ = number_format($discount_summ, 2, '.', '');

							// exit (__($print));

            switch ($print['period_month']) {
                case '01':
                    $print['print_date'] = "2018-01-31";
                    break;
                case '02':
                    $print['print_date'] = "2018-02-28";
                    break;
                case '03':
                    $print['print_date'] = "2018-03-31";
                    break;
                case '04':
                    $print['print_date'] = "2018-04-30";
                    break;
                case '05':
                    $print['print_date'] = "2018-05-31";
                    break;
                case '06':
                    $print['print_date'] = "2018-06-30";
                    break;
                case '07':
                    $print['print_date'] = "2018-07-31";
                    break;
                case '08':
                    $print['print_date'] = "2018-08-31";
                    break;
                case '09':
                    $print['print_date'] = "2018-09-30";
                    break;
                case '10':
                    $print['print_date'] = "2018-10-31";
                    break;
                case '11':
                    $print['print_date'] = "2018-11-30";
                    break;
                case '12':
                    $print['print_date'] = "2018-12-31";
                    break;

                default:
                    $print['print_date'] = "0000-00-00";
                    break;
            }
					// Проверяем сколько оснований указано в Договоре
            $grounds_count = strstr($print['contract_ground'], ",");

					// Если в договоре указано более одного основания, то добавляем $print необходимыми полями
            if (!empty($grounds_count)) {
                $grounds = explode(",", $print['contract_ground']); // Получаем массив с id оснований договора
                $allprice = 0; // общая сумма договора (по основаниям)
                foreach ($grounds as $key => $value) {
                    $gr = Q("SELECT * FROM `#_mdd_grounds` WHERE `id` = ?i", array($value))->row();

                    foreach ($gr as $key1 => $value1) {
                        $print['ground'][$key][$key1] = $value1;
                    }
                    $print['ground'][$key]['summ'] = $gr['price'] * $print['invoice_amount'];
                    $print['ground'][$key]['summ'] = number_format($print['ground'][$key]['summ'], 2, '.', '');
                    $allprice = $allprice + $print['ground'][$key]['summ'];
                }
                $print['allprice'] = number_format($allprice, 2, '.', '');
                $print['allprice_string'] = num2str($print['allprice']);
            }
        }



        if ($print != 0) {
            $date = explode("-", $print['print_date']);

            switch ($date[1]) {
                case '01':
                    $month = 'Января';
                    break;
                case '02':
                    $month = 'Февраля';
                    break;
                case '03':
                    $month = 'Марта';
                    break;
                case '04':
                    $month = 'Апреля';
                    break;
                case '05':
                    $month = 'Мая';
                    break;
                case '06':
                    $month = 'Июня';
                    break;
                case '07':
                    $month = 'Июля';
                    break;
                case '08':
                    $month = 'Августа';
                    break;
                case '09':
                    $month = 'Сентября';
                    break;
                case '10':
                    $month = 'Октября';
                    break;
                case '11':
                    $month = 'Ноября';
                    break;
                case '12':
                    $month = 'Декабря';
                    break;

                default:
                    $month = 'Ошибка';
                    break;
            }
        }

        if ($peni == 1) {
            $allpeni['string'] = num2str($allpeni['peni']);
        }

        if ($disc == 0 && $ind != 'as') {
            $print['contract_summa_string'] = num2str($print['invoice_summa']);
        } elseif ($ind != 'as') {
            $print['contract_summa_string'] = num2str($print['discoint']);
        }

        if (!isset($month)) {
            $month = 0;
            $date = 0;
        }

        if (!isset($allpeni)) {
            $allpeni = 0;
        }

				// барем айдишник клиента из сессии
 /*        $client = Q("SELECT * FROM `#_mdd_renters` as `renter`
				
									LEFT JOIN `#_mdd_contracts` as `contracts`
									ON `renter`.`id` = `contracts`.`renter`

									WHERE `short_name` = ?s", array($_SESSION['login']))->row(); */

        if ($_GET['ind'] == 'as') {
					// находим всю его историю операций
            $history = Q("SELECT * FROM `db_mdd_balance` WHERE `contract_id` = ?i AND `summa` != ?s ORDER BY `id` ASC", array($_GET['id'], '0.00'))->all();

					//проверяет находится ли дата в промежутке запроса для акта сверки
            $start_date = strtotime(str_replace("/", ".", $_GET['start']));
            $end_date = strtotime(str_replace("/", ".", $_GET['end']));
            $first_valid_id = 0;

            for ($i = 0; $i < count($history); $i++) {
                $new_date = strtotime(str_replace("/", "-", $history[$i]['date']));

                if (($start_date <= $new_date && $new_date <= $end_date) || ($end_date <= $new_date && $new_date <= $start_date)) {
                    $history[$i]['valid'] = true;

                    if ($first_valid_id == 0) {
                        $first_valid_id = $history[$i]['id'];
                    }
                } else {
                    $history[$i]['valid'] = false;
                }
            }
				
					// находим первую валидную дату
            $starting_saldo = Q("SELECT `balance`,`summa` FROM `db_mdd_balance` WHERE `id` = ?i ORDER BY `id` ASC", array($first_valid_id))->row();
            $saldo = number_format($starting_saldo['balance'] + $starting_saldo['summa'], 2, '.', '');

            $string = $client['balance'];
            $string < 0 ? $string *= -1 : $string;
            /* $client['string'] = num2str($string); */
        } else {
            $history = $saldo = 0;
        }

        if (isset($print['invoice_date'])) {
            $invoice_day = substr($print['invoice_date'], -2);
        } else {
            $invoice_day = 0;
        }


        return array(
            'print' => $print,
            'month_string' => $month,
            'date' => $date,
            'document' => $ind,
            'pr' => $pr,
            'template' => 'block',
            'disc' => $disc,
            'discount_summ' => $discount_summ,
            'peni' => $peni,
            'allpeni' => $allpeni,
            'history' => $history,
            /* 'client' => $client, */
            'saldo' => $saldo,
            'invoice_day' => $invoice_day
        );
    }
}