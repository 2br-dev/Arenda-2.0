<?php
include_once '../../define.php';

class User
{
    // database connection and table name
    private $conn;
    private $table_name = 'db_mdd_renters';
 
    // object properties
    public $id;
    public $login;
    public $password;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function login($login, $password){
      $host = DB_HOST;
      $user = DB_USER;
      $pass = DB_PASS;
      $db   = DB_BASE;
      $conn = mysqli_connect($host, $user, $pass, $db);

      $query = "SELECT * FROM db_mdd_renters WHERE login='$login' AND password='$password' ";
      $get_login = Q("SELECT `is_admin`, `short_name`,`id` FROM `#_mdd_renters` WHERE `login`=?s AND `password`= ?s", array($login, $password))->row();
      $username_login = $get_login['short_name'];
      
      $result = mysqli_query($conn,$query);
      $rows = mysqli_num_rows($result);

      if (mysqli_num_rows($result) == 1) {

          if($get_login['is_admin'] == 1) {
            echo json_encode(array('result' => 1, 'admin' => 1, 'id' => $get_login['id']));
          } else {
            echo json_encode(array('result' => 1, 'admin' => 0, 'id' => $get_login['id']));
          }	

      } else {
        echo json_encode(array('result' => 0));
      }

      $conn->close();
    }
  
}