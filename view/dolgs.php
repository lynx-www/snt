<?php
include('conf.php');
$dolg = new DataBase();
$sql = "SELECT * FROM street, sector WHERE street.id = sector.street AND sector.status = 1 AND sector.id LIKE '007'";
$dolgs = $dolg->select_sql($sql);
$itog_dolg = 0;
$itog_peny = 0;
echo '<table class="table table-sm table-striped table-reflow">';


foreach($dolgs as $d){
    echo '<thead class="thead">';
    echo '<tr><th style="background: #ccc;">Участок '.$d['id'].'</th><th colspan="5" style="background: #ccc;">'.$d['name'].'</th></tr>';
    //echo '<tr><th>Наименование</th><th>Сумма к оплате</th></tr>';
   echo '</thead>';
    $opl = $dolg->one('plot', 'oplats', $d['id']);
   // $sqlt = "SELECT * FROM oplats WHERE plot LIKE '001'";
    //var_dump($sqlt);
    //$opl = $dolg->select_sql($sqlt);
    foreach($opl as $o){
        //echo '<tbody>';
        echo '<tr><th>'.$o['name'].'</th>
        <th>Сумма к оплате '.$o['pay'].'</th>
        <th>Оплатить до '.$o['date_opl'].'</th>
        <th>Просрочено дней</th><th>Пени</th></tr>';
      $pay = "SELECT oplats.plot as o_plot, oplats.name as o_name, 
      oplats.area as o_area, oplats.pay as o_pay, oplats.date_opl as o_date, cast(payment.date as Date) as Date, payment.* FROM oplats 
      LEFT JOIN payment USING(plot, name) WHERE `oplats`.`plot` LIKE '".$d['id']."' AND oplats.name = '".$o['name']."' ORDER BY `oplats`.`name` ASC ";
        $pays = $dolg->select_sql($pay);
      // var_dump($pays);
      foreach($pays as $p){
         
          $my_diff = $dolg->col_days($o['date_opl'], $p['date']); //По дате платежа считаем кол-во дней просрочки

          if($my_diff > 0){
//var_dump($my_diff);
          $peny = $dolg->itog_peny($d['id'], $o['name']);

          }
          else {$peny = 0; }

       //  $itog_peny = $itog_peny + $peny;
        echo '<tr><td color="red">Оплата </td><td>'.$p['pay'].'</td><td>Дата оплаты: '.$p['date'].'</td>
        <td>'.round($my_diff, 0).'</td><td>'.round($peny, 2).'</td></tr>';
        //$dolg->test_peny($d['id'], $o['name'], $o['date_opl'], $p['date']);
       
      
          $dolg->test_peny($d['id'], $o['name'], $o['date_opl'], $o['pay'], $p['date'], $p['pay']);
        
        
      }  
      $sqlv = "SELECT sum(pay) as pays FROM payment WHERE plot = '".$d['id']."' AND name = '".$o['name']."' GROUP BY name";
      $itog = $dolg->one_param($sqlv);
      $ds = $dolg->sum_dolg($o['pay'], $itog);
      $ps = $dolg->peny_itog($d['id'], $o['name']);
      echo '<tr><td color="red">Итого оплачено </td><td>'.$itog.'</td><td></td></tr>';
      echo '<tr><td color="red">Долг </td><td>'.$ds.'</td><td>Пени: '.$ps.'</td></tr>';
      $itog_dolg = $ds + $itog_dolg;
     // 
     
     
    }
    
}
//test_peny($plot, $name, $srok, $opl, $date=NULL, $opl1)

$sum_peny = $dolg->sum_peny($d['id']);
echo '<tr><th>Общая сумма долга на '.date('d-m-Y').' </th><th>'.$itog_dolg.'</th>
<th>Сумма пеней </th><th>'.round($sum_peny, 2).'</th></tr>';
echo '</table>';
// Считает пени только по первому платежу
?>