<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/database.php';
include_once '../classes/room.php';

$database = new Database();
$db = $database->getConnection();

$rooms = new Room($db);
 
$stmt = $rooms->read();
$num = $stmt->rowCount();
 
if($num > 0){
    $rooms_arr = array();
    $rooms_arr["rooms"] = array();
 
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
 
        $room = array(
            "id"              => $id,
            "number"          => $number,
            "number_scheme"   => $number_scheme,
            "square"          => $square,
            "floor"           => $floor
        );
 
        array_push($rooms_arr["rooms"], $room);
    }
    http_response_code(200);
 
    echo json_encode($rooms_arr);
}
 else
{
  http_response_code(404);

  echo json_encode(
      array("message" => "No renters found.")
  );
}