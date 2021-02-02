<?php
include('conf.php');
$dolg = new DataBase();
$sql = "SELECT * FROM street, sector WHERE street.id = sector.street AND sector.status = 1 AND sector.id LIKE '394'";
$dolgs = $dolg->select_sql($sql);
$itog_dolg = 0;
$itog_peny = 0;
$sum_p = 0;
echo '<table class="table table-sm table-striped table-reflow">';


foreach($dolgs as $d){
    echo '<thead class="thead">';
    echo '<tr><th style="background: #ccc;">Участок '.$d['id'].'</th><th colspan="5" style="background: #ccc;">'.$d['name'].'</th></tr>';
    //echo '<tr><th>Наименование</th><th>Сумма к оплате</th></tr>';	58.26
   echo '</thead>';
    $opl = $dolg->one('plot', 'oplats', $d['id']);

    foreach($opl as $o){
 
        //echo '<tbody>';
        echo '<tr><th>'.$o['name'].'</th>
        <th>Сумма к оплате '.$o['pay'].'</th>
        <th>Оплатить до '.$o['date_opl'].'</th>
        <th>Просрочено дней</th><th>Пени</th></tr>';
      $pay = "SELECT oplats.plot as o_plot, oplats.name as o_name, 
      oplats.area as o_area, oplats.pay as o_pay, oplats.date_opl as o_date, cast(payment.date as Date) as Date, payment.* FROM oplats 
      LEFT JOIN payment USING(plot, name) WHERE `oplats`.`plot` LIKE '".$d['id']."' AND oplats.name = '".$o['name']."' ORDER BY oplats.name, `payment`.`date` ASC ";
        $pays = $dolg->select_sql($pay);
      // var_dump($pays);
      foreach($pays as $p){

          $my_diff = $dolg->col_days($o['date_opl'], $p['date']); //По дате платежа считаем кол-во дней просрочки

          if($my_diff > 0){
//var_dump($my_diff);
          $peny = $dolg->itog_peny($d['id'], $o['name']);

          }
          else {$peny = 0; }
          if(is_null($p['date']) || $p['date'] == null){
            $date = date("Y-m-d", strtotime($p['date']));
      }
          $date = date("Y-m-d", strtotime($p['date']));// Переформатируем дату оплаты

          ///////////////СЧИТАЕМ ПЕНИ/////////////////////////
          $test_peny = $dolg->test_peny($d['id'], $o['name'], $o['date_opl'], $o['pay'], $p['date'], $p['pay']);
          ////////////Забираем из таблицы пеней сумму пеней
          $penies =  $dolg->itogs_peny($d['id'], $o['name'], $p['date'], $o['pay'], round($my_diff, 0), $p['pay']);
          //$penies = 0;
        // $penies = $penies * $my_diff;
        echo '<tr><td color="red">Оплата </td><td>'.$p['pay'].'</td><td>Дата оплаты: '.$p['date'].'</td>
        <td>'.round($my_diff, 0).'</td><td>'.round($penies, 2).'</td></tr>';
        
      }

       
      $sqlv = "SELECT sum(pay) as pays FROM payment WHERE plot = '".$d['id']."' AND name = '".$o['name']."' GROUP BY name";
      $itog = $dolg->one_param($sqlv);
      $ds = $dolg->sum_dolg($o['pay'], $itog);
      $ps = $dolg->peny_itog($d['id'], $o['name']);
      echo '<tr><td color="red">Итого оплачено </td><td>'.$itog.'</td><td></td></tr>';
      echo '<tr><td color="red">Долг </td><td>'.$ds.'</td><td>Пени: </td></tr>';
      $itog_dolg = $ds + $itog_dolg;
      $sum_p = $penies + $sum_p;
     // 
    }
    
    }
    


$sum_peny = $dolg->sum_peny($d['id']);
echo '<tr><th>Общая сумма долга на '.date('d-m-Y').' </th><th>'.$itog_dolg.'</th>
<th>Сумма пеней на '.date('d-m-Y').'</th><th>'.round($sum_p, 2).'</th></tr>';
echo '</table>';
// Считает пени только по первому платежу
?>