<?php
class Date{
  //Функция для принятия даты, если даты нет вернуть текущую
  public function my_date($date = NULL){
    $today = date('Y-m-d');
    if(is_null($date) || $date == null){
      $date = $today;
    }
    return $date;
  }
}
?>