<?php
//phpinfo();
include('../conf.php');
$srok = "2019-06-30";
$date = "2019-08-15";
//$date = null;
//Обход двумерного массива
$user = new DataBase();

$user->test_peny($srok);

/*
$sql = "SELECT * FROM stavka";
$d = $user->select_sql($sql);
$d_begin = $srok;
$array = array();
$i = 0;

foreach($d as $k){
   
    if(($srok >= $k['begin'])  && ($srok <= $k['end'])){     
        $k['begin'] = $srok; 
        array_push($array, ['id' => $k['id'], 'begin' => $k['begin'], 'end' => $k['end'], 'day' => $k['day']]);
        
    }
    //Как же взять середину?

    if(($k['begin'] > $srok) && ($k['end'] <= $date) && ($k['begin'] < $date)){
        array_push($array, ['id' => $k['id'], 'begin' => $k['begin'], 'end' => $k['end'], 'day' => $k['day']]);
    }
    
    //Конец цикла
    if(($date >= $k['begin'])  && ($date <= $k['end'])){     
        $k['end'] = $date; 
            array_push($array, ['id' => $k['id'], 'begin' => $k['begin'], 'end' => $k['end'], 'day' => $k['day']]);

        } 
       if($date < $k['begin'] ){ break;}
        //не учитавыется если день текущий и больше null
    }
    
   

var_dump($array); 
//Далее скопированный массив обрабатываем и считаем пени.
$itog = 0;
foreach($array as $ar){
  $day = $user->col_days($ar['begin'], $ar['end']);
  $days = $day + 1;
  $peny = $days * $ar['day'];
  $itog = $itog + $peny; 
  var_dump($itog);

}
var_dump($itog);

//Необходимо сумму долга умножить на $peny и разделить на 100 
//Далее round (, 2);
//	   $rs = round($rw['cena'] * 0.02167/100, 2); 





*/

?>