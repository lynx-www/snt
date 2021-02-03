<?php

class DataBase {

    function __construct(){
      $dbhost = 'localhost';
      $dbuser = 'root'; 
      $dbpwd  =   '123'; 
      $dbname =   'u0459901_snt';

      $mysqli =new mysqli($dbhost, $dbuser, $dbpwd, $dbname);
      $this->db = $mysqli;
      if($this->db->connect_errno){
        echo "erro connect bd";
        echo "Номер ошибки: " . $this->db->connect_errno . "\n";
        echo "Ошибка: " . $this->db->connect_error . "\n";
        }
        if (!$this->db->set_charset("utf8")) {
            printf("error utf", $this->db->error);
            } else{
               // printf("nabor");
                }
    }

}

/*
class db extends DataBase{
  public function select($sql){
    $array = array();
    $result = $this->db->query($sql);
    while ($row = $result->fetch_array(MYSQLI_BOTH)){
      $array[] = $row;
  }
  return $array;
  }

}

?>