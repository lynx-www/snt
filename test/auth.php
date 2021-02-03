<?php
public function test_peny($plot, $name, $srok, $opl, $date=NULL, $opl1){
  $today = date('Y-m-d');
  if(is_null($date) || $date == null){
    $date = $today;
  }
  if($srok < $date){  //проверяем дату к оплате  и дату платежа, если дата платежа меньше срока, ничего не считать
$sql = "SELECT * FROM stavka";
$d = $this->select_sql($sql);
var_dump($date);
$array = array();


foreach($d as $k){
  $date = strtotime($date);
  $date = date("Y-m-d", $date);
   if($k['end'] == null) {$k['end'] = $today; }
    if(($srok >= $k['begin'])  && ($srok <= $k['end'])){     
        $k['begin'] = $srok; 
        array_push($array, ['id' => $k['id'], 'name' =>$name, 'begin' => $k['begin'], 'end' => $k['end'], 'day' => $k['day'], 'opl' => $date]);
        
    }
    //Как же взять середину?

    if(($k['begin'] > $srok) && ($k['end'] <= $date) && ($k['begin'] < $date)){
        array_push($array, ['id' => $k['id'], 'name' =>$name, 'begin' => $k['begin'], 'end' => $k['end'], 'day' => $k['day'], 'opl' => $date]);
    }
    
    //Конец цикла
    if(($date >= $k['begin'])  && ($date <= $k['end']) || ($k['end'] == null)){     
        $k['end'] = $date; 
            array_push($array, ['id' => $k['id'], 'name' =>$name, 'begin' => $k['begin'], 'end' => $k['end'], 'day' => $k['day'], 'opl' => $date]);

        } 
      // if($date < $k['begin'] ){ break;}
        
    }
    
  }

var_dump($array); 
//Далее скопированный массив обрабатываем и считаем пени.
$itog = 0;
foreach($array as $ar){
  var_dump($ar['end']);
  $day = $this->col_days($ar['begin'], $ar['end']);
  var_dump($day);
  $days = $day + 1;
  $gg = $opl - $opl1;
  if($day > 0 && $gg == 0){
    $gg = $opl;
  }

  $peny = round($gg * $ar['day']/100, 2);
  $peny_itog = $days * $peny;

  if($ar['end'] == null) {}
  $sqls = "INSERT INTO `peny` (`plot`, `name`, `begin`, `end`, `peny`, `opl`, `status`) 
  VALUES ('".$plot."','".$name."', '".$ar['begin']."', '".$ar['end']."',  ".$peny_itog.", '".$date."', 1)";
  var_dump($sqls);

   $this->db->query($sqls);

}
//if($peny_itog == null){$peny_itog = 0;} //Здесь ошибка Undefined variable: peny_itog in
//return $peny_itog;
return $gg;


}