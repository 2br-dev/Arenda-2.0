<?php

function comparePayment($summa, $date, $number, $renter_name, $renter_document, $id) {
  $payment = Q("SELECT * FROM `#_mdd_payments` 
    WHERE `summa` = ?s
    AND `date` = ?s
    AND `rest` = ?s
    AND `renter_name` = ?s
    AND `payment_info` = ?s
    AND `document` = ?s", array($summa, $date, $number, $renter_name, $renter_document, $id))->all();

  if(count($payment) > 0) {
    return false;
  } else {
    return true;
  }

}