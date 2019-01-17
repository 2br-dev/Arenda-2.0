<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/database.php';
include_once '../classes/renters.php';

$database = new Database();
$db = $database->getConnection();

$renters = new Renters($db);
 
$stmt = $renters->read();
$num = $stmt->rowCount();
 
if($num > 0){
    $renters_arr = array();
    $renters_arr["renters"] = array();
 
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
 
        $renter = array(
            "id"              => $id,
            "balance"         => $balance,
            "full_name"       => $full_name,
            "short_name"      => $short_name,
            "form"            => $form,
            "inn"             => $inn,
            "kpp"             => $kpp,
            "ogrn"            => $ogrn,
            "uridich_address" => $uridich_address,
            "post_adress"     => $post_adress,
            "email"           => $email,
            "bank_name"       => $bank_name,
            "bank_rs"         => $bank_rs,
            "bank_ks"         => $bank_ks,
            "bank_bik"        => $bank_bik,
            "chief_position"  => $chief_position,
            "chief_name"      => $chief_name,
            "chief_document"  => $chief_document
        );
 
        array_push($renters_arr["renters"], $renter);
    }
    http_response_code(200);
 
    echo json_encode($renters_arr);
}
 else
{
  http_response_code(404);

  echo json_encode(
      array("message" => "No renters found.")
  );
}