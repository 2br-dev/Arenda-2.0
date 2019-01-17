<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/database.php';
include_once '../classes/contract.php';

$database = new Database();
$db = $database->getConnection();

$contracts = new Contract($db);
 
$stmt = $contracts->read();
$num = $stmt->rowCount();
 
if($num > 0){
    $contracts_arr = array();
    $contracts_arr["contracts"] = array();
 
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
 
        $contract = array(
            "id"             => $id,
            "balance"        => $balance,
            "start_peni"     => $start_peni,
            "datetime"       => $datetime,
            "number"         => $number,
            "status"         => $status,
            "renter"         => $renter,
            "summa"          => $summa,
            "discoint"       => $discoint,
            "start_date"     => $start_date,
            "end_date"       => $end_date,
            "peni"           => $peni,
            "start_arenda"   => $start_arenda,
            "ground"         => $ground,
            "rooms"          => $rooms
        );
        array_push($contracts_arr["contracts"], $contract);
    }
    http_response_code(200);
 
    echo json_encode($contracts_arr);
}
 else
{
  http_response_code(404);

  echo json_encode(
      array("message" => "No contracts found.")
  );
}