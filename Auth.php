<?php

    class DataBase {

    function __construct(){
      $dbhost = 'localhost';
      $dbuser = 'root'; 
      $dbpwd  =   '123'; 
      $dbname =   'u0459901_snt';

      $mysqli =new mysqli($dbhost, $dbuser, $dbpwd, $dbname);
      $this->db = $mysqli;
    }

    public function login($login, $password){
    session_start();
      $enrypt = md5(md5($password));
      $auth = "SELECT * FROM users WHERE login ='".$login."' AND password = '".$enrypt."'";
        //echo $auth;
      $result = $this->db->query($auth);
     // $row = $result->fetch_array();
         /* определение числа рядов в выборке */
      $row_cnt = $result->num_rows;

   // printf("В выборке %d рядов.\n", $row_cnt);
     // if(isset($result)){
      if($row_cnt != 0 ){
          
        $_SESSION['login'] = $login;
        if(isset($_SESSION['login'])){
             header('Location: login.php'); 
            }
        else{ session_start(); 
            session_unset();
            session_destroy(); 
            header('Location: /snt/index.php');
            echo 'error'; 
              exit();
            }
      }
      else{ 
          if($login != '' AND $password !=''){
              echo '<div class="container d-flex justify-content-center h-100">Не правильный логин или пароль</div>';
          }
        }
  }

  public function select($table, $sort=NULL){
    $array = array();
    $sql = "SELECT * FROM {$table}";
    if(isset($sort)){
      $sql .= " ORDER BY {$sort}";
    }
    echo $sql;
    $result = $this->db->query($sql);
    while ($row = $result->fetch_array(MYSQLI_BOTH)){
        $array[] = $row;
    }
    return $array;
  }
  public  function select_sql($sql){
    //echo $sql;
    $array = array();
    $result = $this->db->query($sql);
    while ($row = $result->fetch_array(MYSQLI_BOTH)){
      $array[] = $row;
  }
  return $array;
 }
  public function select_filtr($table, $where=NULL){
    $array = array();
    $sql = "SELECT * FROM {$table} WHERE famely LIKE '".$where."%'";
    $result = $this->db->query($sql);
    while ($row = $result->fetch_array(MYSQLI_BOTH)){
        $array[] = $row;
    }
    return $array;
  }

  public function one($param, $table, $where){
    $array = array();
    $sql = "SELECT * FROM {$table} WHERE {$param} LIKE '".$where."'";
    //echo $sql;
   // $conn = $this->db->query();
    $result = $this->db->query($sql);
    while ($row = $result->fetch_array(MYSQLI_BOTH)){
        $array[] = $row;
    }
    return $array;
  }

  public function one_param($sql){ 
    //echo $sql;
    $result = $this->db->query($sql); 
    $value = $result->fetch_array(MYSQLI_BOTH);

      return isset($value) ? $value[0] : "0";
    
}
  
 public function sum_dolg($opl, $opl1){ //вернуть сумму долга
  return $opl - $opl1;
 }
  public function add($param){  //Добавление записей в таблицу

  }


  public function interval($srok, $date)
  {
    echo "Срок оплаты: ".$srok." Дата оплаты: ".$date."<br>";
    if($srok >= $date){echo "оплата вовремя"; exit; }
    $sql = "SELECT * FROM stavka";
    $result = $this->db->query($sql);
    $srok_ = $srok;
    $i = 0;
    while ($row = $result->fetch_array(MYSQLI_BOTH)){
      echo "begin = ".$row['begin']." end = ".$row['end']." ".$srok_."<br>";
     if($srok_ < $date){
        if(($srok_ >= $row['begin']) && ($srok_ <= $row['end'])){
         
          echo "Нашли нужный период ".$row['begin']." end = ".$row['end']." srok_ = ".$srok_."<br>";
          if($srok_ >= $date){ echo "Закончили "; exit; }
          if(($date >= $row['begin']) && ($date <= $row['end'])){ 
            echo "Нашли конец период ".$row['begin']." end = ".$row['end']." srok_ = ".$date."<br>";
          }
        echo "srok_".$srok_."<br>";
        
        }
      }

          
    }

   

}
public function return_begin($date=NULL){
  if(!isset($date)){$date = date("Y-m-d");}
  $sql = "SELECT * FROM stavka";
  $result = $this->db->query($sql);
 
  while ($row = $result->fetch_array(MYSQLI_BOTH)){
      if(($date >= $row['begin']) && ($date <= $row['end'])){
        $id = $row['id'];
        return $id;
        exit;
      }
   }
}

public function count_day($id_srok, $srok, $id_date){
  $sql = "SELECT * FROM stavka";
  $result = $this->db->query($sql);
 
  while ($row = $result->fetch_array(MYSQLI_BOTH)){
    //От срока вычесть end, получить по периоду кол-во дней и пени. 
    $srok1 = DateTime::createFromFormat('Y-m-d', $srok);
    $end = DateTime::createFromFormat('Y-m-d', $row['end']);
    $i = $srok1->diff($end);
    echo "d = ".$d = $i->format('%d');
    if((($row['id']) <= $id_date) && (($row['id']) >= $srok)){
      echo "Нашли нужный период ".$row['begin']." end = ".$row['end']."<br>";
    }
    
  }
}
/* НЕ ПРАВИЛЬНО РАБОТАЕТ
public function my_diff($date1, $date2){
  if(!isset($date2)){ $date2 = date('Y-m-d');}
  $d1 = DateTime::createFromFormat('Y-m-d', $date1);
  $d2 = DateTime::createFromFormat('Y-m-d', $date2);
$i = $d1->diff($d2);
$d = $i->format('%d');
return $d;
}
*/
//Разница между датами
function col_days($srok, $date_2=null) {

if(is_null($date_2) || $date_2 == null){
  $date_2 = date('Y-m-d');
}
if($srok > $date_2){

  return ''; exit;
}
  else{
    $time_1 = strtotime($srok);
    $time_2 = strtotime($date_2); 
    $diff = abs($time_1 - $time_2);
    $col_days = $diff / 60 / 60 / 24;
    return $col_days;  
  }
}

//Считаем долг по ставке
public function test_end($srok, $date_2){
  if(is_null($date_2) || $date_2 == null){
    $date_2 = date('Y-m-d');
  }
  if($srok > $date_2){
  
    return '+'; 
  }
    else{
//Обработка
$sql = "SELECT * FROM stavka";
$result = $this->db->query($sql);
while ($row = $result->fetch_array(MYSQLI_BOTH)){

  if(($srok >= $row['begin']) && ($srok <= $row['end'])){   //Ищем дату платежа
    $begin = $row['end']; 
    $st1 = $row['stavka'];
    $id1 = $row['id'];
    $day1 = $row['day'];
    }
    if(($date_2 >= $row['begin']) && ($date_2 <= $row['end'])){   //Ищем дату платежа
      $end = $row['begin']; 
      $st2 = $row['stavka'];
      $id2 = $row['id'];
      $day2 = $row['day'];
      }
  }
 
  //Обрабатываем от и до

  $b = $this->col_days($srok, $begin);
  $b1 = $this->col_days($end, $date_2);
 var_dump($b);
  for($i=$st1; $i <= $st1; $i++){
    /* проба вытащить кусок из stavka*/
    $sql = "SELECT day FROM stavka WHERE id = {$i}";
    $result = $this->db->query($sql);
    var_dump($result);
    $row = $result->fetch_array(MYSQLI_BOTH);
    var_dump($row); //0
    //end
    $stavka = $b * $day1;
    $stavka2 = $b1 * $day2;
    var_dump($stavka);
    var_dump($stavka2);
    $itog = $stavka + $stavka2;
  }
  return $begin.' '.$st1.' '.$id1.' '.$end.' '.$st2.' '.$id2.''.$itog;

}
}
/**/
public function test_peny($plot, $name, $srok, $opl, $date=NULL, $opl1){
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
public function insert($sql){
  $result = $this->db->query($sql);
  return $result;
}
//Выбираем пени из таблицы peny
public function itog_peny($plot, $name){
$sql = "SELECT sum(peny) as sum FROM peny WHERE plot = '".$plot."' AND name = '".$name."' GROUP BY name";
$result = $this->db->query($sql);
if ($row = $result->fetch_array(MYSQLI_BOTH)){
  return $row['sum'];
}
else {return 0; }
}

//Получаем сумму пеней
public function sum_peny($plot){
  $sql = "SELECT sum(peny) as sum FROM peny WHERE plot = '".$plot."'";
  $result = $this->db->query($sql);
  if ($row = $result->fetch_array(MYSQLI_BOTH)){
    return $row['sum'];
  }
  else {return 0; }
  }
//Function add_date
function add_day($date, $porog){
  echo date("Y-m-d",$porog);
  $datetime = new DateTime(date("Y-m-d",$date));
  while($date < $porog){
    echo $date.'<br>';
    $datetime->modify('+1 day');
    $date = $datetime->format('Y-m-d');
  }
 
  return $date;
  }

public function start_date($srok){
  echo "Срок оплаты ".$srok."<br>";
  $sql = "SELECT * FROM stavka";
  $result = $this->db->query($sql);
  
  while ($row = $result->fetch_array(MYSQLI_BOTH)){
             // Convert to timestamp
     $begin = strtotime($row['begin']);
     $end = strtotime($row['end']);
     $now = strtotime(date('Y-m-d'));
     if(empty($end)){ $end = $now; }
     $srok_ts = strtotime($srok);

    if(($srok_ts >= $begin) && ($srok_ts <= $end)){
      echo $row['stavka'].' '.$row['day']." begin ".date("Y-m-d",$begin)." end = ".date("Y-m-d",$end)."<br>";
   echo $srok_ts."<br>";
   //echo $d = $this->add_day($srok_ts, $end);
      
    }
}

}



    }


if((isset($_POST['login'])) AND (isset($_POST['password']))){
    $login = $_POST['login'];
    $password = $_POST['password'];

    $obj=new DataBase();
    $obj->login($login, $password);
}
 
?>
