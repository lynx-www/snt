<?php
//realpath (dirname(__FILE__));
include_once "model.php";
include_once "../include.php";

class db extends DataBase{
    //Функция для принятия даты, если даты нет вернуть текущую
    public function select($sql){
      $array = array();
      $result = $this->db->query($sql);
      while ($row = $result->fetch_array(MYSQLI_BOTH)){
        $array[] = $row;
    }
    return $array;
    }

}
/* тест подключения
$db = new db();
echo $db->my_date();
*/