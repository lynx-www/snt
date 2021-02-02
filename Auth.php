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



  //Используется
  //$sql = "SELECT * FROM street, sector WHERE street.id = sector.street AND sector.status = 1 AND sector.id LIKE '394'";
  public  function select_sql($sql){
    //var_dump($sql);
    $array = array();
    $result = $this->db->query($sql);
    while ($row = $result->fetch_array(MYSQLI_BOTH)){
      $array[] = $row;
  }
  return $array;
 }

 //Дата принимается как параметр, если есть, вернуть, если нет, присвоить текущую дату
public function my_date($date = NULL){
  $today = date('Y-m-d');
  if(is_null($date) || $date == null){
    $date = $today;
  }
  return $date;
}


  public function one($param, $table, $where){
    $array = array();
    $sql = "SELECT * FROM {$table} WHERE {$param} LIKE '".$where."'";
   // var_dump($sql);
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
  //Используется
 public function sum_dolg($opl, $opl1){ //вернуть сумму долга
  return $opl - $opl1;
 }
 

//Разница между датами используется
function col_days($srok, $date = null) {
//var_dump($date);
if(is_null($date) || $date == null){ //не сработало
  $date = date('Y-m-d');
  
}

if($srok > $date){

  return ''; exit;
}
  else{
    $time_1 = strtotime($srok);
    $time_2 = strtotime($date); 
    $diff = abs($time_1 - $time_2);
    $col_days = $diff / 60 / 60 / 24;
    return $col_days;  
  }
  return 0;
}

public function peny_itog($plot, $name){
  $sql = "SELECT sum(peny) as peny FROM peny WHERE plot LIKE '".$plot."' AND name = '".$name."'";
  $result = $this->db->query($sql);
  $row = $result->fetch_array(MYSQLI_BOTH);
  return $row['peny'];
 //var_dump($row['peny']);
}

//test_peny($d['id'], $o['name'], $o['date_opl'], $o['pay'], $p['date'], $p['pay']);
public function test_peny($plot, $name, $srok, $opl, $date=NULL, $opl1 = NULL){
  $array = array();
  $today = date('Y-m-d');
  if(is_null($date) || $date == null){
    $date = $today;
  }
  $date = strtotime($date);
  $srok = strtotime($srok);
  if($srok < $date){ //Если просрочка
  $date = date('Y-m-d', $date);
  $srok = date('Y-m-d', $srok);
  $sql = "SELECT * FROM stavka";
  $d = $this->select_sql($sql);
  foreach($d as $k){
    //Сразу $k['end']если null заменяем на текущую дату
    $k['end'] == null ? $today : $k['end'];
    if(is_null($k['end']) || $k['end'] == null){
    $k['end'] = $today;
  }

    if(($date <= $k['begin'])  && ($date <= $k['end'])){ $k['end'] = $date;  $end = $k['id']; }
    if(($srok >= $k['begin'])  && ($srok <= $k['end'])){ $k['begin'] = $srok; }
      //Конец цикла
      if(($date >= $k['begin'])  && ($date <= $k['end'])){     
        $k['end'] = $date; 
            array_push($array, ['id' => $k['id'], 'name' =>$name, 'begin' => $k['begin'], 'end' => $k['end'], 'day' => $k['day'],'pay' => $opl1, 'opl' => $date]);
          continue;
        } 
 //Проверить середину
 if(($k['begin'] > $srok) && ($k['end'] <= $date) && ($k['begin'] < $date)){
  array_push($array, ['id' => $k['id'], 'name' =>$name, 'begin' => $k['begin'], 'end' => $k['end'], 'day' => $k['day'],'pay' => $opl1, 'opl' => $date]);
}
  }


  //$i = $plot.' '.$end.' '.$name.' '.$srok.' '.$date.' проср';
  //var_dump($i);
  //var_dump($array);
  }
  else{ // Иначе вовремя и выходим
   /* $date = date('Y-m-d', $date);
    $srok = date('Y-m-d', $srok);
    $i = $plot.' '.$name.' '.$srok.' '.$date.' нет проср';
    var_dump($i); */
    return true;
   }
//До этого момента все правильно
foreach($array as $ar){
  $sqls = "INSERT INTO `peny` (`plot`, `name`, `begin`, `end`, `peny`, `opl`, `status`) 
  VALUES ('".$plot."','".$name."', '".$ar['begin']."', '".$ar['end']."',  ".$ar['day'].", '".$date."', 1)
  ON DUPLICATE KEY UPDATE `plot` = '".$plot."', `name` = '".$name."', `begin`='".$ar['begin']."',
   `end`='".$ar['end']."', `peny` = '".$ar['day']."', `opl` = '".$date."'";
  //var_dump($sqls);
  $this->db->query($sqls);
}
//var_dump($array);
}



//$penies =  $dolg->itogs_peny($d['id'], $o['name'], $p['date'], round($my_diff, 0), $o['pay']);
public function itogs_peny($plot, $name, $date, $k_opl,  $prosrocka, $pay){
  //Что делать с суммой долга? 
  //$rs = round($rw['cena'] * 0.02167/100, 2);  
  $today = date('Y-m-d');
   //Если нет даты оплаты, то за нее взять текущую
  if(is_null($date) || $date == null){
    $date = $today; 
  }
$sum = $k_opl - $pay;
if($sum == 0){
  $sum = $k_opl;
}
//var_dump($sum);
$date = date("Y-m-d", strtotime($date));
  $var = "SELECT * FROM peny WHERE plot LIKE '".$plot."' AND name = '".$name."' AND opl = '".$date."'";
 
  $result = $this->db->query($var);
  
  while ($row = $result->fetch_array(MYSQLI_BOTH)){
    $k = $sum * $row['peny'] / 100;
    $peny = $prosrocka * $k;
  
    return $peny != 0 ? $peny : 0;
    //return $sum;
  }

//return $sum;

}


public function insert($sql){
 // var_dump($sql);
  $result = $this->db->query($sql);
  return $result;
}
//Выбираем пени из таблицы peny
public function itog_peny($plot, $name){
$sql = "SELECT sum(peny) as sum FROM peny WHERE plot = '".$plot."' AND name = '".$name."' GROUP BY name";
//var_dump($sql);
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


public function test_select($sql=null, $param=null){
  $array = array();
$query = $this->db->prepare($sql); // assuming $mysqli is the connection
$query->bind_param('s', $param);
if(!$query->execute()) return false;
    $res = $query->get_result();
    while($row = $res->fetch_assoc()){
      echo $row['plot'] .'<br>';
   }

$query->close();
}

    }


if((isset($_POST['login'])) AND (isset($_POST['password']))){
    $login = $_POST['login'];
    $password = $_POST['password'];

    $obj=new DataBase();
    $obj->login($login, $password);
}
 

/*
  public function select($table, $sort=NULL){
    $array = array();
    $sql = "SELECT * FROM {$table}";
    if(isset($sort)){2019-08-18
      $sql .= " ORDER BY {$sort}";
    }
    echo $sql;
    $result = $this->db->query($sql);
    while ($row = $result->fetch_array(MYSQLI_BOTH)){
        $array[] = $row;
    }
    return $array;
  }
  */

//Считаем долг по ставке
/*
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
*/

/*
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
*/


/*
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
*/
/*
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
*/
/*
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
      echo "Нашли нужный период ".$row['begin']." = ".$row['end']."<br>";
    }
    
  }
}
*/
/* НЕ ПРАВИЛЬНО РАБОТАЕТ
public function my_diff($date1, $date2){
  if(!isset($date2)){ $date2 = date('Y-m-d');}
  $d1 = DateTime::createFromFormat('Y-m-d', $date1);
  $d2 = DateTime::createFromFormat('Y-m-d', $date2);
$i = $d1->diff($d2);
$d = $i->format('%d');
return $d;
}

================
git add file
git commit -m "initial commit"
git push
*/
?>
