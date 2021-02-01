<? public function test_peny($plot, $name, $srok, $opl, $date=NULL, $opl1){
  if(is_null($date) || $date == null){
    $date = date('Y-m-d');
  }
$sql = "SELECT * FROM stavka";
$d = $this->select_sql($sql);

$array = array();


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
    if(($date >= $k['begin'])  && ($date <= $k['end']) || ($k['end'] == null)){     
        $k['end'] = $date; 
            array_push($array, ['id' => $k['id'], 'begin' => $k['begin'], 'end' => $k['end'], 'day' => $k['day']]);

        } 
       if($date < $k['begin'] ){ break;}
        
    }
    
   

//var_dump($array); 
//Далее скопированный массив обрабатываем и считаем пени.
$itog = 0;
foreach($array as $ar){
  $day = $this->col_days($ar['begin'], $ar['end']);
  $days = $day + 1;
  $gg = $opl - $opl1;
  if($day > 0 && $gg == 0){
    $gg = $opl;
  }

  $peny = round($gg * $ar['day']/100, 2);
  $peny_itog = $days * $peny;
  if($ar['end'] == null) {}
  $sqls = "INSERT INTO `peny` (`plot`, `name`, `begin`, `end`, `peny`) VALUES ('".$plot."','".$name."', '".$ar['begin']."', '".$ar['end']."',  ".$peny_itog.");";
  var_dump($sqls);
  //$this->insert($sqls);
   $this->db->query($sqls);


}

return $peny_itog;


}

?>
